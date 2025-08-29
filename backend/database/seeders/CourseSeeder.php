<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Category;
use App\Models\Admin;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a category
        $category = Category::firstOrCreate([
            'name' => 'برنامه نویسی',
            'slug' => 'programming'
        ], [
            'description' => 'دوره های برنامه نویسی و توسعه نرم افزار'
        ]);

        // Get or create an admin
        $admin = Admin::firstOrCreate([
            'email' => 'admin@edicodes.com'
        ], [
            'name' => 'مدیر سیستم',
            'password' => bcrypt('password')
        ]);

        // Create sample courses
        $courses = [
            [
                'title' => 'آموزش کامل Laravel',
                'description' => 'یادگیری کامل فریمورک Laravel از مبتدی تا پیشرفته',
                'short_description' => 'دوره جامع Laravel برای توسعه دهندگان',
                'price' => 500000, // 500,000 Toman
                'status' => 'published',
                'featured' => true,
                'duration' => '20 ساعت',
                'level' => 'intermediate',
                'category_id' => $category->id,
                'admin_id' => $admin->id
            ],
            [
                'title' => 'مبانی برنامه نویسی',
                'description' => 'آموزش اصول اولیه برنامه نویسی برای مبتدیان',
                'short_description' => 'شروع سفر برنامه نویسی',
                'price' => 0, // Free course
                'status' => 'published',
                'featured' => false,
                'duration' => '10 ساعت',
                'level' => 'beginner',
                'category_id' => $category->id,
                'admin_id' => $admin->id
            ],
            [
                'title' => 'توسعه API پیشرفته',
                'description' => 'ساخت API های حرفه ای و امن',
                'short_description' => 'طراحی و پیاده سازی API های RESTful',
                'price' => 800000, // 800,000 Toman
                'status' => 'published',
                'featured' => true,
                'duration' => '25 ساعت',
                'level' => 'advanced',
                'category_id' => $category->id,
                'admin_id' => $admin->id
            ]
        ];

        foreach ($courses as $courseData) {
            // Generate slug from title
            $courseData['slug'] = \Illuminate\Support\Str::slug($courseData['title']);
            
            $course = Course::create($courseData);

            // Create sample content for each course
            $this->createCourseContent($course);
        }
    }

    /**
     * Create sample content for a course.
     */
    private function createCourseContent(Course $course): void
    {
        $contents = [
            [
                'title' => 'معرفی دوره',
                'content' => 'در این دوره شما با مفاهیم اصلی آشنا خواهید شد.',
                'type' => 'text',
                'order' => 1,
                'is_free' => true
            ],
            [
                'title' => 'نصب و راه اندازی',
                'content' => 'مراحل نصب و پیکربندی محیط توسعه',
                'type' => 'text',
                'order' => 2,
                'is_free' => true
            ],
            [
                'title' => 'ویدیوی آموزشی - بخش اول',
                'content' => 'آموزش عملی مفاهیم اولیه',
                'type' => 'video',
                'order' => 3,
                'video_duration' => 1800, // 30 minutes
                'is_free' => false
            ],
            [
                'title' => 'تمرین عملی',
                'content' => 'تمرینات عملی برای تثبیت یادگیری',
                'type' => 'text',
                'order' => 4,
                'is_free' => false
            ]
        ];

        foreach ($contents as $contentData) {
            CourseContent::create(array_merge($contentData, ['course_id' => $course->id]));
        }
    }
}
