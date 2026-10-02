<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /** Active devices. To remove one, call POST logout with {"id": ...}. */
    public function index(Request $request): JsonResponse
    {
        $currentTokenId = $request->user()->currentAccessToken()->id;

        $devices = $request->user()->devices()->active()->get()->map(fn ($d) => [
            'id' => $d->id,
            'device_name' => $d->device_name,
            'platform' => $d->platform,
            'browser' => $d->browser,
            'ip_address' => $d->ip_address,
            'last_used_at' => $d->last_used_at,
            'is_current' => $d->token_id === $currentTokenId,
        ]);

        return response()->json(['data' => $devices]);
    }
}
