<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Enrollment;

class VideoStreamMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $course = $request->route('course');
        $content = $request->route('content');
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // Check if content exists and belongs to course
        if (!$content || $content->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'محتوای مورد نظر یافت نشد'
            ], 404);
        }

        // Check if content is video
        if ($content->type !== 'video') {
            return response()->json([
                'success' => false,
                'message' => 'این محتوا ویدیو نیست'
            ], 400);
        }

        // Check if user is enrolled in the course
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('payment_status', 'completed')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'شما در این دوره ثبت نام نکرده اید'
            ], 403);
        }

        // Check if enrollment is active
        if (!$enrollment->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی شما به این دوره منقضی شده است'
            ], 403);
        }

        return $next($request);
    }
}














