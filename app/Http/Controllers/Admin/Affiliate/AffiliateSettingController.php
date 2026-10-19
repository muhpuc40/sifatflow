<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AffiliateSettingRequest;
use App\Models\AffiliateSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Singleton settings page: one row (AffiliateSetting::current()), no index/create/destroy. */
class AffiliateSettingController extends Controller
{
    /** GET /affiliate/settings */
    public function edit(): View
    {
        return view('admin.affiliate.settings', ['settings' => AffiliateSetting::current()->load('updatedBy')]);
    }

    /** PUT /affiliate/settings */
    public function update(AffiliateSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        AffiliateSetting::current()->update($data);

        return back()->with('success', 'Affiliate settings updated.');
    }
}
