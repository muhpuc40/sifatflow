<?php

namespace Database\Seeders;

use App\Enums\AmountType;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseContent;
use App\Models\CourseCurriculum;
use App\Models\CourseResource;
use App\Models\CourseReview;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Sample courses covering many cases (php artisan db:seed --class=CourseDemoSeeder).
 *
 * Scenario map (slug -> what it tests):
 *  full-stack-web-development   published, 20% discount, price history, 3 installments, 3 stages
 *  laravel-for-beginners        published, no discount, full payment (no installments)
 *  react-nextjs-mastery         published, flat discount, flat installments
 *  flutter-app-development      published, 10% discount, price history, 2 installments
 *  python-for-data-science      published, no discount
 *  machine-learning-foundations published, every batch is finished -> no open batch
 *  photoshop-masterclass        published, flat discount, only 5 seats
 *  ui-ux-design-with-figma      published, NO price yet, no thumbnail
 *  digital-marketing-essentials published, FREE course (price 0)
 *  nodejs-api-development       published, has no batch at all
 *  brand-identity-design        draft                 -> hidden from the API
 *  advanced-seo                 archived              -> hidden from the API
 *  wordpress-development        published but inactive -> hidden from the API
 *  ethical-hacking              draft only category   -> category hidden from the API
 *  old-video-editing            soft deleted          -> hidden everywhere
 *  category "Career Skills" has no course            -> hidden from the API
 */
class CourseDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::withTrashed()->exists()) {
            return;
        }

        $cat = [];
        foreach ([
            'web-development' => 'Web Development', 'mobile-app' => 'Mobile App', 'data-science-ai' => 'Data Science & AI',
            'graphic-design' => 'Design', 'digital-marketing' => 'Digital Marketing',
            'cyber-security' => 'Cyber Security', 'career-skills' => 'Career Skills',
        ] as $slug => $name) {
            $cat[$slug] = CourseCategory::create(['slug' => $slug, 'name' => $name, 'description' => "{$name} courses"]);
        }

        $web3 = ['Beginning' => ['HTML Basics', 'CSS Styling', 'JavaScript Basics'],
                 'Intermediate' => ['PHP & MySQL', 'Laravel Framework', 'REST API'],
                 'Advanced' => ['React Frontend', 'Deployment']];
        $two = fn (string $a, string $b, string $c, string $d) => ['Beginning' => [$a, $b], 'Intermediate' => [$c, $d]];

        //            category           title                           slug                          level      status       active  price  dType      dVal  oldPrice plan     thumbnail  stages
        $courses = [
            ['web-development',  'Full Stack Web Development',  'full-stack-web-development',  'Offline', 'published', true,  12000, 'percent', 20, 15000, 'three', true, $web3],
            ['web-development',  'Laravel for Beginners',        'laravel-for-beginners',       'Online',  'published', true,  6000,  null,      0,  null,  'full',  true, $two('PHP Basics', 'OOP in PHP', 'Laravel Routing', 'Eloquent ORM')],
            ['web-development',  'React & Next.js Mastery',      'react-nextjs-mastery',        'Online',  'published', true,  9000,  'flat',    1000, null, 'flat',  true, $two('React Basics', 'Hooks & State', 'Next.js Pages', 'Next.js API')],
            ['mobile-app',       'Flutter App Development',      'flutter-app-development',     'Offline', 'published', true,  10000, 'percent', 10, 11000, 'two',   true, $two('Dart Language', 'Flutter Widgets', 'State Management', 'Publish to Play Store')],
            ['data-science-ai',  'Python for Data Science',      'python-for-data-science',     'Online',  'published', true,  8000,  null,      0,  null,  'two',   true, $two('Python Basics', 'NumPy & Pandas', 'Data Visualization', 'Mini Project')],
            ['data-science-ai',  'Machine Learning Foundations', 'machine-learning-foundations','Online',  'published', true,  14000, 'percent', 25, null,  'three', true, $two('Statistics', 'Linear Models', 'Classification', 'Model Deployment')],
            ['graphic-design',   'Photoshop Masterclass',        'photoshop-masterclass',       'Offline', 'published', true,  4500,  'flat',    500, null,  'full',  true, $two('Photoshop Tools', 'Photo Editing', 'Retouching', 'Social Media Posts')],
            ['graphic-design',   'UI/UX Design with Figma',      'ui-ux-design-with-figma',     'Online',  'published', true,  null,  null,      0,  null,  'none',  false, $two('Design Basics', 'Figma Tools', 'Wireframing', 'Prototype')],
            ['digital-marketing','Digital Marketing Essentials', 'digital-marketing-essentials','Online',  'published', true,  0,     null,      0,  null,  'full',  true, $two('Marketing Basics', 'Facebook Ads', 'Google Ads', 'Analytics')],
            ['web-development',  'Node.js API Development',      'nodejs-api-development',      'Online',  'published', true,  7000,  null,      0,  null,  'two',   true, $two('Node Basics', 'Express', 'MongoDB', 'Authentication')],
            ['graphic-design',   'Brand Identity Design',        'brand-identity-design',       'Online',  'draft',     true,  8000,  null,      0,  null,  'two',   true, $two('Logo Basics', 'Color Theory', 'Brand Guide', 'Mockups')],
            ['digital-marketing','Advanced SEO',                 'advanced-seo',                'Online',  'archived',  true,  5000,  null,      0,  null,  'full',  true, $two('On-page SEO', 'Technical SEO', 'Link Building', 'SEO Audit')],
            ['web-development',  'WordPress Development',        'wordpress-development',       'Online',  'published', false, 5500,  null,      0,  null,  'full',  false, $two('WP Basics', 'Themes', 'Plugins', 'WooCommerce')],
            ['cyber-security',   'Ethical Hacking',              'ethical-hacking',             'Online',  'draft',     true,  9500,  null,      0,  null,  'two',   true, $two('Networking', 'Kali Linux', 'Web Attacks', 'Reporting')],
            ['graphic-design',   'Old Video Editing',            'old-video-editing',           'Offline', 'published', true,  3000,  null,      0,  null,  'full',  true, $two('Premiere Basics', 'Cutting', 'Color', 'Export')],
        ];

        foreach ($courses as [$catSlug, $title, $slug, $level, $status, $active, $price, $dType, $dValue, $oldPrice, $plan, $thumb, $stages]) {
            $course = Course::create([
                'course_categories_id' => $cat[$catSlug]->id,
                'title' => $title,
                'slug' => $slug,
                'description' => "{$title}: learn step by step with live classes, recorded lessons, exams and assignments. Perfect for beginners.",
                'thumbnail' => $thumb ? "https://picsum.photos/seed/{$slug}/640/360" : null,
                'preview_video' => $thumb ? 'https://example.com/preview/'.$slug.'.mp4' : null,
                'level' => $level,
                'status' => $status,
                'is_active' => $active,
            ]);

            $modules = $this->curriculum($course, $stages);

            if ($price !== null) {
                if ($oldPrice) {
                    $course->changePrice($oldPrice, null, 0, false);   // price history: old price stays in the table
                }
                $course->changePrice($price, $dType ? AmountType::from($dType) : null, $dValue, false);
                $this->paymentPlan($course, $plan, $modules);
            }

            foreach (['Build real projects', 'Understand the core concepts', 'Work with real tools', 'Be ready for a job or freelancing'] as $i => $t) {
                $course->learningOutcomes()->create(['title' => $t, 'sort_order' => $i + 1, 'is_active' => $i < 3 || $slug !== 'laravel-for-beginners']);
            }
            $course->learningOutcomes()->create(['title' => 'Hidden outcome (inactive)', 'sort_order' => 9, 'is_active' => false]);

            $course->faqs()->create(['question' => 'Do I need any experience?', 'answer' => 'No, we start from zero.', 'sort_order' => 1]);
            $course->faqs()->create(['question' => 'Will I get a certificate?', 'answer' => 'Yes, after you finish the course.', 'sort_order' => 2]);
            $course->faqs()->create(['question' => 'Hidden question (inactive)', 'answer' => 'This FAQ is switched off.', 'sort_order' => 3, 'is_active' => false]);
        }

        Course::where('slug', 'old-video-editing')->first()?->delete();

        $this->reviews();
    }

    /** Stages, modules and every item type. Returns the modules in order. */
    private function curriculum(Course $course, array $stages): array
    {
        $all = [];
        $week = 0;
        $sort = 0;

        foreach ($stages as $stageTitle => $moduleTitles) {
            $stage = CourseCurriculum::create([
                'course_list_id' => $course->id, 'title' => $stageTitle, 'description' => "{$stageTitle} stage of {$course->title}",
                'duration' => 30, 'milestone_value' => 25, 'sort_order' => ++$sort,
            ]);

            foreach ($moduleTitles as $m => $title) {
                $week++;
                $module = $stage->modules()->create(['title' => $title, 'week' => $week, 'objective' => "Learn {$title}", 'sort_order' => $m + 1]);
                $all[] = $module;

                foreach ([1, 2] as $part) {
                    $video = CourseContent::create([
                        'name' => "{$title} - Part {$part}",
                        'thumbnail' => 'https://picsum.photos/seed/'.$course->slug.$week.$part.'/320/180',
                        'url' => 'https://example.com/videos/'.$course->slug.'-'.$week.'-'.$part.'.mp4',
                    ]);
                    $module->items()->create([
                        'item_type' => 'recorded', 'course_content_id' => $video->id, 'sort_order' => $part,
                        'is_preview' => $week === 1 && $part === 1,   // first lesson is a free preview
                    ]);
                }
                $module->items()->create(['item_type' => 'live', 'objective' => "Live class: {$title}", 'sort_order' => 3]);
                $notes = CourseResource::create(['name' => "{$title} notes", 'url' => 'https://example.com/notes/'.$course->slug.'-'.$week.'.pdf']);
                $module->items()->create(['item_type' => 'resources', 'course_resource_id' => $notes->id, 'sort_order' => 4]);
                $module->items()->create(['item_type' => 'assignment', 'objective' => "Assignment: {$title}", 'sort_order' => 5]);
                $module->items()->create(['item_type' => 'exam', 'objective' => "Exam: {$title}", 'sort_order' => 6]);

                if ($week === 3) {   // an item switched off by the admin (must not show on the website)
                    $module->items()->create(['item_type' => 'live', 'objective' => 'Extra live class (inactive)', 'sort_order' => 7, 'is_active' => false]);
                }
            }
        }

        // a module switched off by the admin
        $last = CourseCurriculum::where('course_list_id', $course->id)->orderByDesc('sort_order')->first();
        $hidden = $last->modules()->create(['title' => 'Bonus module (inactive)', 'week' => $week + 1, 'sort_order' => 99, 'is_active' => false]);
        $hidden->items()->create(['item_type' => 'live', 'objective' => 'Bonus live', 'sort_order' => 1]);

        return $all;
    }

    private function paymentPlan(Course $course, string $plan, array $modules): void
    {
        /** @var \App\Models\CoursePrice $price */
        $price = $course->prices()->where('is_active', true)->first();
        $at = fn (int $i) => $modules[min($i, count($modules) - 1)]->id;

        $rules = match ($plan) {
            'two' => [['Admission', 'percent', 50, null], ['Second installment', 'percent', 50, $at(2)]],
            'three' => [['Admission', 'percent', 40, null], ['Second installment', 'percent', 30, $at(2)], ['Final installment', 'percent', 30, $at(4)]],
            'flat' => [['Admission', 'flat', 3000, null], ['Second installment', 'flat', 3000, $at(2)], ['Final installment', 'flat', 2000, $at(3)]],
            default => [],   // full payment
        };

        foreach ($rules as $i => [$name, $type, $amount, $moduleId]) {
            $price->paymentRules()->create([
                'course_list_id' => $course->id, 'course_modules_id' => $moduleId,
                'rule_name' => $name, 'amount_type' => $type, 'amount' => $amount, 'sort_order' => $i + 1,
            ]);
        }
    }

    private function reviews(): void
    {
        $students = Student::orderBy('id')->pluck('id')->all();
        if (! $students) {
            return;
        }

        $comments = ['Very clear teaching.', 'Great course, worth the money.', 'Good, but I wanted more projects.', 'The live classes helped a lot.', 'Excellent support from the instructor.'];
        // slug => ratings of approved reviews
        $plan = [
            'full-stack-web-development' => [5, 5, 4, 5, 4, 3],
            'laravel-for-beginners' => [5, 4, 4],
            'react-nextjs-mastery' => [4, 5],
            'flutter-app-development' => [5],
            'python-for-data-science' => [4, 3],
            'machine-learning-foundations' => [5, 4, 4, 5],
        ];

        foreach ($plan as $slug => $ratings) {
            $course = Course::where('slug', $slug)->first();
            $n = 0;
            foreach ($ratings as $i => $rating) {
                CourseReview::create(['course_list_id' => $course->id, 'student_id' => $students[$i], 'rating' => $rating, 'comment' => $comments[$i % 5], 'status' => 'approved']);
                $n = $i + 1;
            }
            // one waiting for approval and one rejected (both must stay hidden)
            CourseReview::create(['course_list_id' => $course->id, 'student_id' => $students[$n], 'rating' => 2, 'comment' => 'Waiting for admin approval.', 'status' => 'pending']);
            CourseReview::create(['course_list_id' => $course->id, 'student_id' => $students[$n + 1], 'rating' => 1, 'comment' => 'Rejected review.', 'status' => 'rejected']);
        }
    }
}
