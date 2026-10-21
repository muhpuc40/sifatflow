<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Affiliate\AttributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffiliateClickController extends Controller
{
    /**
     * POST /api/v1/ref/{ref_code}/click
     * Call this once when a visitor lands on a ref link. Always 200 - a bad/dead ref code,
     * or the program being off, just comes back as {"tracked": false} so the frontend can
     * carry on without an affiliate cookie rather than having to handle an error.
     *
     * Body (all optional):
     *   visitor_token  - send back the value from a previous call so repeat visits/pageviews
     *                    from the same browser stay one visitor; omit on the very first call
     *                    and store the one returned here (as a cookie, for cookie_days).
     *   course_id      - the course page this ref link landed on, if any.
     *   landing_url    - the full URL that was opened.
     */
    public function click(Request $request, string $refCode, AttributionService $attribution): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['nullable', 'string', 'max:64'],
            'course_id' => ['nullable', 'integer'],
            'landing_url' => ['nullable', 'string', 'max:500'],
        ]);

        $result = $attribution->recordClick(
            $refCode,
            $request,
            $data['visitor_token'] ?? null,
            $data['course_id'] ?? null,
            $data['landing_url'] ?? null,
        );

        return response()->json($result);
    }

    /**
     * GET /api/v1/ref/attribution?visitor_token=...
     * Lets the frontend show "you're shopping via X" before the visitor signs up. Always 200;
     * {"attributed": false} for a missing/expired/dead token, same "fail quiet" rule as click().
     */
    public function attribution(Request $request, AttributionService $attribution): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['required', 'string', 'max:64'],
        ]);

        return response()->json($attribution->preview($data['visitor_token']));
    }
}
