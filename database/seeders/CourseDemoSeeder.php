<?php

namespace Database\Seeders;

use App\Enums\AmountType;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseContent;
use App\Models\CourseCurriculum;
use App\Models\CourseResource;
use Illuminate\Database\Seeder;

/** Sample courses for trying the admin panel: php artisan db:seed --class=CourseDemoSeeder */
class CourseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $web = CourseCategory::firstOrCreate(['slug' => 'web-development'], ['name' => 'Web Development']);
        $design = CourseCategory::firstOrCreate(['slug' => 'graphic-design'], ['name' => 'Graphic Design']);

        $video = CourseContent::firstOrCreate(['name' => 'Intro video'], ['url' => 'https://example.com/intro.mp4']);
        $slides = CourseResource::firstOrCreate(['name' => 'Starter slides'], ['url' => 'https://example.com/slides.pdf']);

        $list = [
            [$web, 'Full Stack Web Development', 'full-stack-web-development', 'Offline', 'published', 12000, 'percent', 20],
            [$web, 'Laravel for Beginners', 'laravel-for-beginners', 'Online', 'published', 6000, null, 0],
            [$design, 'Photoshop Masterclass', 'photoshop-masterclass', 'Offline', 'draft', 4500, 'flat', 500],
            [$design, 'Brand Identity Design', 'brand-identity-design', 'Online', 'draft', 8000, null, 0],
        ];

        foreach ($list as [$cat, $title, $slug, $level, $status, $price, $dType, $dValue]) {
            if (Course::withTrashed()->where('slug', $slug)->exists()) {
                continue;
            }

            $course = Course::create([
                'course_categories_id' => $cat->id,
                'title' => $title,
                'slug' => $slug,
                'description' => "Learn {$title} step by step with live and recorded classes.",
                'level' => $level,
                'status' => $status,
                'is_active' => true,
            ]);

            $stage = CourseCurriculum::create(['course_list_id' => $course->id, 'title' => 'Beginning', 'sort_order' => 1, 'duration' => 30]);
            $first = null;
            foreach (['HTML', 'CSS', 'JavaScript'] as $i => $name) {
                $module = $stage->modules()->create(['title' => $name, 'week' => $i + 1, 'sort_order' => $i + 1]);
                $first ??= $module;
                $module->items()->create(['item_type' => 'recorded', 'course_content_id' => $video->id, 'course_resource_id' => $slides->id, 'sort_order' => 1, 'is_preview' => $i === 0]);
                $module->items()->create(['item_type' => 'exam', 'sort_order' => 2]);
            }

            $course->changePrice($price, $dType ? AmountType::from($dType) : null, $dValue, false);
            /** @var \App\Models\CoursePrice $current */
            $current = $course->prices()->where('is_active', true)->first();
            $current->paymentRules()->create(['course_list_id' => $course->id, 'rule_name' => 'Admission', 'amount_type' => 'percent', 'amount' => 50, 'sort_order' => 1]);
            $current->paymentRules()->create(['course_list_id' => $course->id, 'course_modules_id' => $first->id, 'rule_name' => 'Second installment', 'amount_type' => 'percent', 'amount' => 50, 'sort_order' => 2]);

            foreach (['Build real websites', 'Work with databases', 'Deploy to a server'] as $i => $t) {
                $course->learningOutcomes()->create(['title' => $t, 'sort_order' => $i + 1]);
            }
            $course->faqs()->create(['question' => 'Do I need coding experience?', 'answer' => 'No, we start from zero.', 'sort_order' => 1]);
        }
    }
}
