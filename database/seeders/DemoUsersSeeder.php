<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Test accounts for local development (all fake). Password for all: Password@123
 *
 *   admin@sifatflow.com
 *   instructor1..5@sifatflow.com
 *   student1..24@sifatflow.com
 *     student1  = normal, verified            student3 = suspended
 *     student4  = NOT verified yet            student24 = deleted (soft)
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguarded(function () {
            $password = Hash::make('Password@123');   // hashed once, so seeding stays fast
            $verified = ['password' => $password, 'verified_at' => now(), 'verified_channel' => 'email'];

            Admin::firstOrCreate(['email' => 'admin@sifatflow.com'], $verified + [
                'name' => 'Super Admin',
                'phone' => '01700000000',
                'status' => 'active',
            ]);
            Admin::firstOrCreate(['email' => 'admin1@sifatflow.com'], $verified + [
                'name' => 'Admin 1',
                'phone' => '01700000002',
                'status' => 'active',
            ]);
            Admin::firstOrCreate(['email' => 'admin2@sifatflow.com'], $verified + [
                'name' => 'Admin 2',
                'phone' => '01700000003',
                'status' => 'active',
            ]);
            $instructors = [
                [
                    'Nusrat Jahan',
                    'Senior Web Instructor',
                    'Full stack developer with 8 years of teaching experience.'
                ],
                [
                    'Tanvir Ahmed',
                    'Mobile App Instructor',
                    'Flutter and Android trainer.'
                ],
                [
                    'Farhana Akter',
                    'Data Science Instructor',
                    'Python, statistics and machine learning.'
                ],
                [
                    'Imran Hossain',
                    'Design Instructor',
                    'UI/UX and brand identity designer.'
                ],
                [
                    'Rakib Hasan',
                    'Marketing Instructor',
                    'SEO and digital marketing consultant.'
                ],
            ];
            foreach ($instructors as $i => [$name, $designation, $bio]) {
                $n = $i + 1;
                Instructor::firstOrCreate(['email' => "instructor{$n}@sifatflow.com"], $verified + [
                    'name' => $name,
                    'phone' => '017000001' . sprintf('%02d', $n),
                    'designation' => $designation,
                    'bio' => $bio,
                    'status' => 'active',
                ]);
            }

            $names = [
                'Rahim Uddin',
                'Karim Hossain',
                'Sumaiya Akter',
                'Jahid Hasan',
                'Mehedi Hasan',
                'Tasnim Jahan',
                'Sabbir Ahmed',
                'Nadia Islam',
                'Arif Rahman',
                'Mim Chowdhury',
                'Shakil Mahmud',
                'Rima Sultana',
                'Faisal Karim',
                'Lamia Khatun',
                'Naim Hossain',
                'Puja Das',
                'Rubel Mia',
                'Sharmin Akter',
                'Zahid Hasan',
                'Tamanna Rahman',
                'Ovi Khan',
                'Maliha Noor',
                'Sohel Rana',
                'Deleted Student',
            ];
            foreach ($names as $i => $name) {
                $n = $i + 1;
                $student = Student::firstOrCreate(['email' => "student{$n}@sifatflow.com"], [
                    'name' => $name,
                    'phone' => '017000002' . sprintf('%02d', $n),
                    'password' => $password,
                    'verified_at' => $n === 4 ? null : now(),
                    'verified_channel' => $n === 4 ? null : 'email',
                    'roll' => $n <= 12 ? sprintf('SF-2026-%04d', $n) : null,   // roll is given after the first enrollment
                    'gender' => $n % 2 ? 'male' : 'female',
                    'status' => $n === 3 ? 'suspended' : 'active',
                    'address' => ['Dhaka', 'Chattogram', 'Rajshahi', 'Sylhet', 'Khulna'][$n % 5],
                ]);

                if ($n === 24) {
                    $student->delete();
                }
            }
        });
    }
}
