<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\UserDevice;
use App\Models\UserLoginInfo;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'students' => Student::count(),
                'instructors' => Instructor::count(),
                'admins' => Admin::count(),
                // Devices signed in through the API and used in the last 5 minutes.
                'online' => UserDevice::online()->count(),
            ],
            'recentLogins' => UserLoginInfo::latest('created_at')->limit(10)->get(),
        ]);
    }
}
