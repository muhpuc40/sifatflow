<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\Admin;
use App\Models\UserLoginInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/** Admin panel login (session based). The API login for apps lives in Api\AuthController. */
class LoginController extends Controller
{
    public function show(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $login = trim($request->input('login'));
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        /** @var Admin|null $admin */
        $admin = Admin::where($field, $login)->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password)) {
            $this->log($request, $admin, 'failed', 'wrong_password');

            return back()->withErrors(['login' => 'These credentials do not match our records.'])->onlyInput('login');
        }

        if (!$admin->isActive()) {
            $this->log($request, $admin, 'blocked', 'suspended');

            return back()->withErrors(['login' => 'Your account is not active.'])->onlyInput('login');
        }

        Auth::guard('web')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        $admin->forceFill(['last_login_at' => now()])->save();
        $this->log($request, $admin, 'success');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function log(Request $request, ?Admin $admin, string $status, ?string $reason = null): void
    {
        UserLoginInfo::create([
            'user_type' => 'admin',
            'user_id' => $admin?->id,
            'login_identifier' => $request->input('login'),
            'status' => $status,
            'failure_reason' => $reason,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
