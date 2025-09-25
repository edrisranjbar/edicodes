<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class VideoStreamController extends Controller
{
    /**
     * Stream video content securely.
     */
    public function stream(Request $request, Course $course, CourseContent $content): Response
    {
        // Verify content belongs to course and is video
        if ($content->course_id !== $course->id || $content->type !== 'video') {
            abort(404);
        }

        if (!$content->video_path || !Storage::exists($content->video_path)) {
            abort(404);
        }

        $filePath = Storage::path($content->video_path);
        $fileSize = Storage::size($content->video_path);
        $fileName = basename($content->video_path);

        // Get file info
        $fileInfo = pathinfo($filePath);
        $extension = strtolower($fileInfo['extension']);

        // Set appropriate content type
        $contentTypes = [
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm'
        ];

        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';

        // Handle range requests for video streaming
        $range = $request->header('Range');
        $response = response();

        if ($range) {
            $ranges = $this->parseRange($range, $fileSize);
            if (empty($ranges)) {
                return response('Requested Range Not Satisfiable', 416);
            }

            $firstRange = $ranges[0];
            $response->header('Content-Range', 'bytes ' . $firstRange['start'] . '-' . $firstRange['end'] . '/' . $fileSize);
            $response->header('Accept-Ranges', 'bytes');
            $response->header('Content-Length', $firstRange['end'] - $firstRange['start'] + 1);
            $response->setStatusCode(206);
        } else {
            $response->header('Content-Length', $fileSize);
            $response->header('Accept-Ranges', 'bytes');
        }

        // Set headers to prevent caching and downloading
        $response->header('Content-Type', $contentType);
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'DENY');
        
        // Prevent download by setting content disposition
        $response->header('Content-Disposition', 'inline; filename="' . $fileName . '"');

        // Stream the file
        if ($range && isset($firstRange)) {
            $this->streamRange($filePath, $firstRange['start'], $firstRange['end']);
        } else {
            $this->streamFile($filePath);
        }

        return $response;
    }

    /**
     * Parse HTTP Range header.
     */
    private function parseRange(string $range, int $fileSize): array
    {
        $ranges = [];
        $range = str_replace('bytes=', '', $range);
        $rangeParts = explode(',', $range);

        foreach ($rangeParts as $rangePart) {
            $rangePart = trim($rangePart);
            if (strpos($rangePart, '-') === false) {
                continue;
            }

            list($start, $end) = explode('-', $rangePart);

            if ($start === '') {
                $start = $fileSize - $end;
                $end = $fileSize - 1;
            } elseif ($end === '') {
                $end = $fileSize - 1;
            }

            $start = (int) $start;
            $end = (int) $end;

            if ($start >= 0 && $end < $fileSize && $start <= $end) {
                $ranges[] = ['start' => $start, 'end' => $end];
            }
        }

        return $ranges;
    }

    /**
     * Stream a specific range of the file.
     */
    private function streamRange(string $filePath, int $start, int $end): void
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            abort(500);
        }

        fseek($handle, $start);
        $length = $end - $start + 1;
        $buffer = 8192; // 8KB chunks

        while ($length > 0 && !feof($handle)) {
            $readLength = min($buffer, $length);
            $data = fread($handle, $readLength);
            echo $data;
            flush();
            $length -= $readLength;
        }

        fclose($handle);
    }

    /**
     * Stream the entire file.
     */
    private function streamFile(string $filePath): void
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            abort(500);
        }

        $buffer = 8192; // 8KB chunks

        while (!feof($handle)) {
            $data = fread($handle, $buffer);
            echo $data;
            flush();
        }

        fclose($handle);
    }
}















