<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Database\Seeder;

/** Test accounts for local development. Password for all: Password@123 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $common = ['password' => 'Password@123', 'email_verified_at' => now()];

        Admin::forceCreate($common + [
            'name' => 'Super Admin',
            'email' => 'admin@sifatflow.com',
            'phone' => '01733000001',
            'status' => 'active',
        ]);

        Instructor::forceCreate($common + [
            'name' => 'Nusrat Jahan',
            'email' => 'instructor1@sifatflow.com',
            'phone' => '01722000001',
            'designation' => 'Senior Instructor',
            'status' => 'active',
        ]);
        Instructor::forceCreate($common + [
            'name' => 'Tanvir Ahmed',
            'email' => 'instructor2@sifatflow.com',
            'phone' => '01722000002',
            'designation' => 'Instructor',
            'status' => 'active',
        ]);

        Student::forceCreate($common + [
            'name' => 'Minhaj Uddin Hassan',
            'email' => 'student1@sifatflow.com',
            'phone' => '01818173025',
            'roll' => 'SF-2026-0001',
            'status' => 'active',
        ]);
        Student::forceCreate($common + [
            'name' => 'Karim Hossain',
            'email' => 'student2@sifatflow.com',
            'phone' => '01711000002',
            'status' => 'active',
        ]);
        Student::forceCreate($common + [
            'name' => 'Suspended Student',
            'email' => 'student3@sifatflow.com',
            'phone' => '01711000003',
            'status' => 'suspended',
        ]);
    }
}
