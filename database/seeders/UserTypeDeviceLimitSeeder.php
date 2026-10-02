<?php

namespace Database\Seeders;

use App\Models\UserTypeDeviceLimit;
use Illuminate\Database\Seeder;

class UserTypeDeviceLimitSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['student' => 2, 'instructor' => 2, 'admin' => 3] as $type => $max) {
            UserTypeDeviceLimit::updateOrCreate(['user_type' => $type], ['max_devices' => $max]);
        }
    }
}
