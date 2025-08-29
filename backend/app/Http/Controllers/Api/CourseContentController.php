<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseContentController extends Controller
{
    /**
     * Store a newly created content block.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => ['required', Rule::in(['video', 'text'])],
            'order' => 'nullable|integer|min:0',
            'video_path' => 'nullable|string',
            'video_duration' => 'nullable|integer|min:0',
            'is_free' => 'boolean'
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_free'] = $request->boolean('is_free', false);

        // Auto-assign order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = $course->contents()->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $content = CourseContent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'محتوای دوره با موفقیت ایجاد شد',
            'data' => $content
        ], 201);
    }

    /**
     * Display the specified content block.
     */
    public function show(Course $course, CourseContent $content): JsonResponse
    {
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
     * Update the specified content block.
     */
    public function update(Request $request, Course $course, CourseContent $content): JsonResponse
    {
        // Verify content belongs to course
        if ($content->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'type' => ['sometimes', 'required', Rule::in(['video', 'text'])],
            'order' => 'nullable|integer|min:0',
            'video_path' => 'nullable|string',
            'video_duration' => 'nullable|integer|min:0',
            'is_free' => 'boolean'
        ]);

        if (isset($validated['is_free'])) {
            $validated['is_free'] = $request->boolean('is_free');
        }

        $content->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'محتوای دوره با موفقیت بروزرسانی شد',
            'data' => $content
        ]);
    }

    /**
     * Remove the specified content block.
     */
    public function destroy(Course $course, CourseContent $content): JsonResponse
    {
        // Verify content belongs to course
        if ($content->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        // Delete video file if exists
        if ($content->type === 'video' && $content->video_path) {
            if (Storage::exists($content->video_path)) {
                Storage::delete($content->video_path);
            }
        }

        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'محتوای دوره با موفقیت حذف شد'
        ]);
    }

    /**
     * Reorder content blocks.
     */
    public function reorder(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'content_orders' => 'required|array',
            'content_orders.*.id' => 'required|exists:course_contents,id',
            'content_orders.*.order' => 'required|integer|min:0'
        ]);

        foreach ($validated['content_orders'] as $item) {
            CourseContent::where('id', $item['id'])
                ->where('course_id', $course->id)
                ->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'ترتیب محتوا با موفقیت بروزرسانی شد'
        ]);
    }

    /**
     * Upload video file for course content.
     */
    public function uploadVideo(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB max
            'content_id' => 'required|exists:course_contents,id'
        ]);

        $content = CourseContent::where('id', $request->content_id)
            ->where('course_id', $course->id)
            ->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        if ($content->type !== 'video') {
            return response()->json([
                'success' => false,
                'message' => 'این محتوا برای ویدیو نیست'
            ], 400);
        }

        // Delete old video if exists
        if ($content->video_path && Storage::exists($content->video_path)) {
            Storage::delete($content->video_path);
        }

        // Store new video
        $videoPath = $request->file('video')->store('courses/' . $course->id . '/videos', 'private');

        // Update content with video path
        $content->update([
            'video_path' => $videoPath,
            'type' => 'video'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ویدیو با موفقیت آپلود شد',
            'data' => [
                'video_path' => $videoPath,
                'content_id' => $content->id
            ]
        ]);
    }
}



