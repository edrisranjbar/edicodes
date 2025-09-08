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
     * Display a listing of course contents.
     */
    public function index(Course $course): JsonResponse
    {
        $contents = $course->contents()->orderBy('order')->get();
        
        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * Store a newly created content block with video.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:204800', // 200MB max
            'order' => 'nullable|integer|min:0',
            'is_free' => 'nullable|boolean'
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_free'] = $request->boolean('is_free', false);

        // Auto-assign order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = $course->contents()->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('courses/' . $course->id . '/videos', 'public');
            $validated['video_path'] = $videoPath;
        }

        $content = CourseContent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'ویدیو با موفقیت آپلود شد',
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
     * Update the specified content block with video.
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

        // Debug logging
        \Log::info('Update content called', [
            'request_method' => $request->method(),
            'has_video' => $request->hasFile('video'),
            'has_video_alt' => $request->file('video') !== null,
            'files' => $request->file(),
            'all_data' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'file_exists' => $request->hasFile('video') ? 'yes' : 'no'
        ]);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:204800', // 200MB max
            'order' => 'nullable|integer|min:0',
            'is_free' => 'nullable|boolean'
        ]);

        if (isset($validated['is_free'])) {
            $validated['is_free'] = $request->boolean('is_free');
        }

        // Handle video upload if provided
        $videoFile = $request->file('video');
        if ($videoFile && $videoFile->isValid()) {
            \Log::info('Video file detected and valid, processing upload');

            // Delete old video if exists
            if ($content->video_path && Storage::exists($content->video_path)) {
                Storage::delete($content->video_path);
                \Log::info('Old video deleted: ' . $content->video_path);
            }

            // Store new video
            $videoPath = $videoFile->store('courses/' . $course->id . '/videos', 'public');
            $validated['video_path'] = $videoPath;
            \Log::info('New video stored at: ' . $videoPath);
        } else {
            \Log::info('No valid video file detected in request', [
                'video_file_exists' => $videoFile !== null,
                'is_valid' => $videoFile ? $videoFile->isValid() : 'N/A'
            ]);
        }

        $content->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'ویدیو با موفقیت بروزرسانی شد',
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
        if ($content->video_path && Storage::exists($content->video_path)) {
            Storage::delete($content->video_path);
        }

        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'ویدیو با موفقیت حذف شد'
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

}
