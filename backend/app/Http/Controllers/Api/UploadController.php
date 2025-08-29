<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload an image file
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|image|max:5120', // 5MB max
            ]);

            if (!$request->hasFile('file')) {
                return response()->json([
                    'message' => 'No file uploaded or file field missing'
                ], 400);
            }
            
            $file = $request->file('file');
            
            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Invalid file upload: ' . $file->getErrorMessage()
                ], 400);
            }
            
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            
            // Store in the public disk under uploads/images folder
            $path = $file->storeAs('uploads/images', $filename, 'public');
            
            if (!$path) {
                return response()->json([
                    'message' => 'Failed to store the file'
                ], 500);
            }
            
            // Return the full URL to the uploaded file
            $url = asset('storage/' . $path);
            
            return response()->json([
                'url' => $url,
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a course thumbnail
     */
    public function uploadCourseThumbnail(Request $request)
    {
        try {
            $request->validate([
                'thumbnail' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
            ]);

            if (!$request->hasFile('thumbnail')) {
                return response()->json([
                    'success' => false,
                    'message' => 'فایل تصویر انتخاب نشده است'
                ], 400);
            }
            
            $file = $request->file('thumbnail');
            
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فایل نامعتبر است: ' . $file->getErrorMessage()
                ], 400);
            }
            
            $extension = $file->getClientOriginalExtension();
            $filename = 'course_thumbnail_' . Str::uuid() . '.' . $extension;
            
            // Store in the public disk under uploads/courses/thumbnails folder
            $path = $file->storeAs('uploads/courses/thumbnails', $filename, 'public');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در ذخیره فایل'
                ], 500);
            }
            
            // Return the full URL to the uploaded file
            $url = asset('storage/' . $path);
            
            return response()->json([
                'success' => true,
                'message' => 'تصویر با موفقیت آپلود شد',
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Course thumbnail upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطا در آپلود تصویر: ' . $e->getMessage()
            ], 500);
        }
    }
} 