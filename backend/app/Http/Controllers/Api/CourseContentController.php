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
     * Store a newly created content block.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => ['required', Rule::in(['video', 'text', 'file'])],
            'order' => 'nullable|integer|min:0',
            'video_path' => 'nullable|string',
            'video_duration' => 'nullable|integer|min:0',
            'file_path' => 'nullable|string',
            'file_name' => 'nullable|string|max:255',
            'file_size' => 'nullable|integer',
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
            'type' => ['sometimes', 'required', Rule::in(['video', 'text', 'file'])],
            'order' => 'nullable|integer|min:0',
            'video_path' => 'nullable|string',
            'video_duration' => 'nullable|integer|min:0',
            'file_path' => 'nullable|string',
            'file_name' => 'nullable|string|max:255',
            'file_size' => 'nullable|integer',
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

        // Delete file if exists
        if ($content->type === 'file' && $content->file_path) {
            if (Storage::exists($content->file_path)) {
                Storage::delete($content->file_path);
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

    /**
     * Upload file for course content.
     */
    public function uploadFile(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar|max:51200', // 50MB max
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

        if ($content->type !== 'file') {
            return response()->json([
                'success' => false,
                'message' => 'این محتوا برای فایل نیست'
            ], 400);
        }

        // Delete old file if exists
        if ($content->file_path && Storage::exists($content->file_path)) {
            Storage::delete($content->file_path);
        }

        // Store new file
        $filePath = $request->file('file')->store('courses/' . $course->id . '/files', 'private');
        $fileName = $request->file('file')->getClientOriginalName();
        $fileSize = $request->file('file')->getSize();

        // Update content with file information
        $content->update([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'type' => 'file'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'فایل با موفقیت آپلود شد',
            'data' => [
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'content_id' => $content->id
            ]
        ]);
    }

    /**
     * Create content with file upload in one request.
     */
    public function storeWithFile(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => ['required', Rule::in(['video', 'text', 'file'])],
            'order' => 'nullable|integer|min:0',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar|max:51200',
            'is_free' => 'boolean'
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_free'] = $request->boolean('is_free', false);

        // Auto-assign order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = $course->contents()->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        // Handle video upload
        if ($request->hasFile('video') && $validated['type'] === 'video') {
            $videoPath = $request->file('video')->store('courses/' . $course->id . '/videos', 'private');
            $validated['video_path'] = $videoPath;
        }

        // Handle file upload
        if ($request->hasFile('file') && $validated['type'] === 'file') {
            $filePath = $request->file('file')->store('courses/' . $course->id . '/files', 'private');
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $request->file('file')->getClientOriginalName();
            $validated['file_size'] = $request->file('file')->getSize();
        }

        $content = CourseContent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'محتوای دوره با موفقیت ایجاد شد',
            'data' => $content
        ], 201);
    }

    /**
     * Get content with file URLs for streaming/download.
     */
    public function getContentWithFiles(Course $course, CourseContent $content): JsonResponse
    {
        // Verify content belongs to course
        if ($content->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        // Add file URLs to content
        if ($content->type === 'video' && $content->video_path) {
            $content->video_url = route('admin.courses.content.video', ['course' => $course->id, 'content' => $content->id]);
        }

        if ($content->type === 'file' && $content->file_path) {
            $content->file_url = route('admin.courses.content.file', ['course' => $course->id, 'content' => $content->id]);
        }

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }
}




