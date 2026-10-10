<?php

namespace Database\Seeders;

use App\Enums\ItemType;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Instructor;
use Illuminate\Database\Seeder;

/**
 * Sample batches for trying the admin panel (run CourseDemoSeeder first):
 *   php artisan db:seed --class=BatchDemoSeeder
 */
class BatchDemoSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'full-stack-web-development')->first() ?? Course::first();

        if (! $course || Batch::where('course_list_id', $course->id)->exists()) {
            return;
        }

        $instructor = Instructor::firstOrCreate(
            ['email' => 'demo.instructor@example.com'],
            ['name' => 'Demo Instructor', 'password' => 'Password@123'],
        );

        // make sure the first module has a live class, an exam and an assignment
        $modules = CourseModule::whereIn('course_curriculum_id', $course->curricula()->select('id'))->orderBy('sort_order')->get();

        foreach ($modules as $module) {
            foreach ([ItemType::Live, ItemType::Assignment, ItemType::Exam] as $type) {
                if (! $module->items()->where('item_type', $type->value)->exists()) {
                    $module->items()->create(['item_type' => $type->value, 'objective' => ucfirst($type->value).' of '.$module->title, 'sort_order' => 10]);
                }
            }
        }

        $running = Batch::create([
            'course_list_id' => $course->id, 'name' => 'Batch 1', 'code' => 'FSW-1', 'instructor_id' => $instructor->id,
            'start_date' => now()->subWeeks(2), 'approx_end_date' => now()->addMonths(4), 'enroll_deadline' => now()->subWeek(),
            'seat_limit' => 40, 'status' => 'running',
        ]);
        Batch::create([
            'course_list_id' => $course->id, 'name' => 'Batch 2', 'code' => 'FSW-2', 'instructor_id' => $instructor->id,
            'start_date' => now()->addWeeks(3), 'approx_end_date' => now()->addMonths(6), 'enroll_deadline' => now()->addWeeks(2),
            'seat_limit' => 30, 'status' => 'upcoming',
        ]);

        $first = $modules->first();
        $live = $first->items()->where('item_type', 'live')->first();
        $recorded = $first->items()->where('item_type', 'recorded')->first();

        $held = $running->classes()->create([
            'course_module_items_id' => $live->id, 'class_start_time' => now()->subDays(5)->setTime(20, 0),
            'instructor_id' => $instructor->id, 'status' => 'held',
        ]);
        $held->liveInfo()->create(['batch_list_id' => $running->id, 'platform' => 'zoom', 'meeting_url' => 'https://zoom.us/j/123456789', 'is_started' => true]);

        $next = $running->classes()->create([
            'course_module_items_id' => $live->id, 'class_start_time' => now()->addDays(2)->setTime(20, 0),
            'instructor_id' => $instructor->id,
        ]);
        $next->liveInfo()->create(['batch_list_id' => $running->id, 'platform' => 'meet', 'meeting_url' => 'https://meet.google.com/abc-defg-hij']);

        if ($recorded) {
            $running->classes()->create(['course_module_items_id' => $recorded->id, 'class_start_time' => now()->addDays(4)->setTime(9, 0), 'duration_minutes' => 60]);
        }

        $running->exams()->create([
            'course_module_items_id' => $first->items()->where('item_type', 'exam')->first()->id, 'title' => 'HTML exam',
            'exam_date' => now()->addWeek(), 'start_time' => '10:00', 'duration_minutes' => 60, 'total_marks' => 50, 'pass_marks' => 20,
        ]);
        $running->assignments()->create([
            'course_module_items_id' => $first->items()->where('item_type', 'assignment')->first()->id, 'title' => 'Build a landing page',
            'assigned_at' => now()->subDays(2), 'due_at' => now()->addDays(5), 'total_marks' => 20, 'status' => 'published',
        ]);
    }
}
