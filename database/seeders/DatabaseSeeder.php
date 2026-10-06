<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Needed in every environment
        $this->call(UserTypeDeviceLimitSeeder::class);

        // Test accounts only on a local machine
        if (app()->environment('local')) {
            $this->call(DemoUsersSeeder::class);
            $this->call(CourseDemoSeeder::class);

        }
    }
}
