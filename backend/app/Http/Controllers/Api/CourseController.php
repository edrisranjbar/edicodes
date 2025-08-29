<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of published courses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['category', 'admin'])
            ->where('status', 'published')
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by price (free or paid)
        if ($request->has('price_type')) {
            if ($request->price_type === 'free') {
                $query->where('price', 0);
            } elseif ($request->price_type === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        $courses = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Display a listing of all courses for admin (including drafts and archived).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Course::with(['category', 'admin'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by admin
        if ($request->has('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        $courses = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'thumbnail' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'featured' => 'nullable|boolean',
            'duration' => 'nullable|string|max:100',
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'category_id' => 'nullable|exists:categories,id',
            'admin_id' => 'required|exists:admins,id'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        // Handle featured field conversion
        if (isset($validated['featured'])) {
            if (is_string($validated['featured'])) {
                $validated['featured'] = in_array($validated['featured'], ['1', 'true', 'on']);
            }
        } else {
            $validated['featured'] = $request->boolean('featured', false);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $extension = $file->getClientOriginalExtension();
            $filename = 'course_thumbnail_' . Str::uuid() . '.' . $extension;
            
            // Store in the public disk under uploads/courses/thumbnails folder
            $path = $file->storeAs('uploads/courses/thumbnails', $filename, 'public');
            
            if ($path) {
                $validated['thumbnail'] = $path;
            }
        }

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'دوره با موفقیت ایجاد شد',
            'data' => $course->load(['category', 'admin'])
        ], 201);
    }

    /**
     * Display the specified course with public data.
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        // Check if user is authenticated and enrolled
        $isEnrolled = false;
        $user = $request->user();
        
        if ($user) {
            $isEnrolled = $course->isEnrolledBy($user);
        }

        // Load basic course data
        $course->load(['category', 'admin']);

        // If user is enrolled, load full content
        if ($isEnrolled) {
            $course->load(['contents' => function ($query) {
                $query->orderBy('order');
            }]);
        } else {
            // Load only free content for preview
            $course->load(['contents' => function ($query) {
                $query->where('is_free', true)->orderBy('order');
            }]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'course' => $course,
                'is_enrolled' => $isEnrolled,
                'can_access_full_content' => $isEnrolled
            ]
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'sometimes|required|integer|min:0',
            'thumbnail' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
            'status' => ['sometimes', 'required', Rule::in(['draft', 'published', 'archived'])],
            'featured' => 'nullable|boolean',
            'duration' => 'nullable|string|max:100',
            'level' => ['sometimes', 'required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'category_id' => 'nullable|exists:categories,id'
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (isset($validated['featured'])) {
            if (is_string($validated['featured'])) {
                $validated['featured'] = in_array($validated['featured'], ['1', 'true', 'on']);
            }
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail && Storage::exists($course->thumbnail)) {
                Storage::delete($course->thumbnail);
            }
            
            $file = $request->file('thumbnail');
            $extension = $file->getClientOriginalExtension();
            $filename = 'course_thumbnail_' . Str::uuid() . '.' . $extension;
            
            // Store in the public disk under uploads/courses/thumbnails folder
            $path = $file->storeAs('uploads/courses/thumbnails', $filename, 'public');
            
            if ($path) {
                $validated['thumbnail'] = $path;
            }
        }

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'دوره با موفقیت بروزرسانی شد',
            'data' => $course->load(['category', 'admin'])
        ]);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): JsonResponse
    {
        // Delete associated content files
        $videoContents = $course->contents()->where('type', 'video')->get();
        foreach ($videoContents as $content) {
            if ($content->video_path && Storage::exists($content->video_path)) {
                Storage::delete($content->video_path);
            }
        }

        // Delete thumbnail if exists
        if ($course->thumbnail && Storage::exists($course->thumbnail)) {
            Storage::delete($course->thumbnail);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'دوره با موفقیت حذف شد'
        ]);
    }

    /**
     * Get course content by ID (for enrolled users only).
     */
    public function getContent(Request $request, Course $course, CourseContent $content): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // Check if user is enrolled
        if (!$course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'شما در این دوره ثبت نام نکرده اید'
            ], 403);
        }

        // Verify content belongs to course
        if ($content->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * Stream video content (for enrolled users only).
     */
    public function streamVideo(Request $request, Course $course, CourseContent $content): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // Check if user is enrolled
        if (!$course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'شما در این دوره ثبت نام نکرده اید'
            ], 403);
        }

        // Verify content belongs to course and is video
        if ($content->course_id !== $course->id || $content->type !== 'video') {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        if (!$content->video_path || !Storage::exists($content->video_path)) {
            return response()->json([
                'success' => false,
                'message' => 'فایل ویدیو یافت نشد'
            ], 404);
        }

        // Return secure video URL (this should be handled by a middleware that prevents direct download)
        return response()->json([
            'success' => true,
            'data' => [
                'video_url' => $content->video_url,
                'title' => $content->title,
                'duration' => $content->video_duration
            ]
        ]);
    }

    /**
     * Display the specified course for admin (shows all data).
     */
    public function adminShow(Request $request, Course $course): JsonResponse
    {
        // Load all course data for admin
        $course->load(['category', 'admin', 'contents' => function ($query) {
            $query->orderBy('order');
        }]);

        // Add enrollment count
        $course->students_count = $course->enrollments()->where('payment_status', 'completed')->count();

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }
}
