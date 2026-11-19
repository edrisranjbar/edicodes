<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseImportApiTest extends TestCase
{
    use RefreshDatabase;

    private function course(): Course
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin);

        return Course::create([
            'title' => 'Python', 'slug' => 'python', 'description' => 'Learn Python',
            'admin_id' => $admin->id, 'level' => 'beginner',
        ]);
    }

    public function test_text_lessons_round_trip_without_video(): void
    {
        $course = $this->course();
        $lesson = ['title' => 'مقدمه', 'content' => "print('<hello>')\nدرس پایتون",
            'order' => 1, 'is_free' => true];
        $this->postJson("/api/admin/courses/{$course->id}/contents", $lesson)
            ->assertCreated()->assertJsonPath('data.content', $lesson['content']);
        $this->getJson("/api/admin/courses/{$course->id}/contents")
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.is_free', true);
    }

    public function test_empty_lessons_are_rejected(): void
    {
        $course = $this->course();
        $this->postJson("/api/admin/courses/{$course->id}/contents", ['title' => 'Empty'])
            ->assertUnprocessable()->assertJsonValidationErrors(['content', 'video']);
    }

    public function test_large_html_lessons_can_replace_plain_text_and_round_trip(): void
    {
        $course = $this->course();
        $lesson = $course->contents()->create(['title' => 'HTML lesson', 'content' => 'Old text', 'order' => 1, 'is_free' => true]);
        $html = '<article><h1>Python</h1><pre><code>' . str_repeat('print("hello")' . "\n", 6000) . '</code></pre></article>';
        $this->putJson("/api/admin/courses/{$course->id}/contents/{$lesson->id}", [
            'content' => $html, 'content_format' => 'html',
        ])->assertOk()->assertJsonPath('data.content_format', 'html')->assertJsonPath('data.content', $html);
        $this->assertSame($lesson->id, $course->contents()->first()->id);
        $this->assertSame($html, $lesson->fresh()->content);
    }

    public function test_video_only_uploads_still_work(): void
    {
        Storage::fake('public');
        $course = $this->course();
        $this->postJson("/api/admin/courses/{$course->id}/contents", [
            'title' => 'Video', 'video' => UploadedFile::fake()->create('lesson.mp4', 10, 'video/mp4'),
        ])->assertCreated();
        Storage::disk('public')->assertExists($course->contents()->first()->video_path);
    }

    public function test_normal_users_cannot_create_lessons(): void
    {
        $course = $this->course();
        $this->actingAs(User::factory()->create(), 'sanctum');
        $this->postJson("/api/admin/courses/{$course->id}/contents", [
            'title' => 'Forbidden', 'content' => 'Text',
        ])->assertForbidden();
    }

    public function test_inactive_admins_cannot_create_lessons(): void
    {
        $course = $this->course();
        Sanctum::actingAs(Admin::factory()->inactive()->create());
        $this->postJson("/api/admin/courses/{$course->id}/contents", [
            'title' => 'Forbidden', 'content' => 'Text',
        ])->assertForbidden();
    }

    public function test_explicit_slug_supports_persian_course_titles_and_rejects_duplicates(): void
    {
        $course = $this->course();
        $payload = ['title' => 'آموزش پایتون', 'slug' => 'python-programming',
            'description' => 'دوره رایگان', 'price' => 0, 'status' => 'draft',
            'level' => 'beginner', 'admin_id' => $course->admin_id];
        $this->postJson('/api/admin/courses', $payload)
            ->assertCreated()->assertJsonPath('data.slug', 'python-programming');
        $this->postJson('/api/admin/courses', $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('slug');
    }

    public function test_guests_cannot_create_lessons(): void
    {
        $this->postJson('/api/admin/courses/1/contents', ['title' => 'Text', 'content' => 'Hello'])
            ->assertUnauthorized();
    }
}
