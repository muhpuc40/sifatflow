<?php

namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\UserTypeDeviceLimit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceLimitController extends Controller
{
    public function index(): View
    {
        $limits = UserTypeDeviceLimit::all()->keyBy('user_type');
        $rows = collect($this->types())->map(fn(string $type) => [
            'type' => $type,
            'limit' => $limits->get($type),
        ]);
        return view('admin.security.device-limits', ['rows' => $rows]);
    }

    public function update(Request $request, string $type): RedirectResponse
    {
        abort_unless(in_array($type, $this->types(), true), 404);
        $data = $request->validate([
            'max_devices' => ['required', 'integer', 'min:1', 'max:20'],
        ]);
        UserTypeDeviceLimit::updateOrCreate(
            ['user_type' => $type],
            ['max_devices' => $data['max_devices']],
        );
        return back()->with('success', ucfirst($type) . ' device limit updated.');
    }

    private function types(): array
    {
        return array_keys(Relation::morphMap());
    }
}