<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Enums\AffiliateStatus;
use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    /** GET /affiliates */
    public function index(Request $request): View
    {
        $filter = $request->input('status');

        $profiles = AffiliateProfile::query()
            ->with('affiliate:id,name,email,status')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->input('q').'%';
                $q->where('ref_code', 'like', $term)
                    ->orWhereHas('affiliate', fn ($a) => $a->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($filter, fn ($q) => $q->where('status', $filter))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.affiliate.index', [
            'profiles' => $profiles,
            'filter' => $filter,
        ]);
    }

    /** PATCH /affiliates/{profile}/approve - only a pending affiliate can be approved. */
    public function approve(Request $request, AffiliateProfile $profile): RedirectResponse
    {
        if ($profile->status !== AffiliateStatus::Pending) {
            return back()->with('error', 'Only a pending affiliate can be approved.');
        }

        $profile->update([
            'status' => AffiliateStatus::Approved,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Affiliate approved.');
    }

    /** PATCH /affiliates/{profile}/reject - only a pending affiliate can be rejected. */
    public function reject(Request $request, AffiliateProfile $profile): RedirectResponse
    {
        if ($profile->status !== AffiliateStatus::Pending) {
            return back()->with('error', 'Only a pending affiliate can be rejected.');
        }

        $profile->update([
            'status' => AffiliateStatus::Rejected,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Affiliate rejected.');
    }

    /** PATCH /affiliates/{profile}/suspend - only an approved affiliate can be suspended. */
    public function suspend(AffiliateProfile $profile): RedirectResponse
    {
        if ($profile->status !== AffiliateStatus::Approved) {
            return back()->with('error', 'Only an approved affiliate can be suspended.');
        }

        $profile->update(['status' => AffiliateStatus::Suspended]);

        return back()->with('success', 'Affiliate suspended.');
    }

    /** PATCH /affiliates/{profile}/reactivate - only a suspended affiliate can be reactivated. */
    public function reactivate(AffiliateProfile $profile): RedirectResponse
    {
        if ($profile->status !== AffiliateStatus::Suspended) {
            return back()->with('error', 'Only a suspended affiliate can be reactivated.');
        }

        $profile->update(['status' => AffiliateStatus::Approved]);

        return back()->with('success', 'Affiliate reactivated.');
    }
}
