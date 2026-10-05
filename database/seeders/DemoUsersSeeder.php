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
        \Illuminate\Database\Eloquent\Model::unguarded(function () {
            $common = ['password' => 'Password@123', 'verified_at' => now(), 'verified_channel' => 'email'];

            Admin::firstOrCreate(['email' => 'admin@sifatflow.com'], $common + [
                'name' => 'Super Admin',
                'email' => 'admin@sifatflow.com',
                'phone' => '01818173025',
                'status' => 'active',
            ]);

            Instructor::firstOrCreate(['email' => 'instructor1@sifatflow.com'], $common + [
                'name' => 'Nusrat Jahan',
                'email' => 'instructor1@sifatflow.com',
                'phone' => '01722000001',
                'designation' => 'Senior Instructor',
                'status' => 'active',
            ]);
            Instructor::firstOrCreate(['email' => 'instructor2@sifatflow.com'], $common + [
                'name' => 'Tanvir Ahmed',
                'email' => 'instructor2@sifatflow.com',
                'phone' => '01722000002',
                'designation' => 'Instructor',
                'status' => 'active',
            ]);

            Student::firstOrCreate(['email' => 'student1@sifatflow.com'], $common + [
                'name' => 'Minhaj Uddin Hassan',
                'email' => 'student1@sifatflow.com',
                'phone' => '01818173025',
                'roll' => 'SF-2026-0001',
                'status' => 'active',
            ]);
            Student::firstOrCreate(['email' => 'student2@sifatflow.com'], $common + [
                'name' => 'Shariar Noman',
                'email' => 'student2@sifatflow.com',
                'phone' => '01707835443',
                'status' => 'active',
            ]);
            Student::firstOrCreate(['email' => 'student3@sifatflow.com'], $common + [
                'name' => 'Suspended Student',
                'email' => 'student3@sifatflow.com',
                'phone' => '01711000003',
                'status' => 'suspended',
            ]);
        });
    }
}
