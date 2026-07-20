<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YouTubeVideo;
use Illuminate\Http\Request;

class YouTubeVideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = YouTubeVideo::where('is_active', true)
            ->orderBy('position')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'url', 'thumbnail', 'position']);

        return response()->json(['videos' => $videos]);
    }

    public function adminIndex(Request $request)
    {
        $videos = YouTubeVideo::orderBy('position')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'url', 'thumbnail', 'position', 'is_active']);

        return response()->json(['videos' => $videos]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'thumbnail' => ['required', 'url'],
            'position' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $video = YouTubeVideo::create(array_merge([
            'position' => 0,
            'is_active' => true,
        ], $data));

        return response()->json([
            'success' => true,
            'message' => 'کلیپ یوتیوب با موفقیت ذخیره شد.',
            'video' => $video,
        ]);
    }

    public function update(Request $request, YouTubeVideo $youtubeVideo)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'thumbnail' => ['required', 'url'],
            'position' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $youtubeVideo->update($data);

        return response()->json([
            'success' => true,
            'message' => 'کلیپ یوتیوب با موفقیت بروز شد.',
            'video' => $youtubeVideo,
        ]);
    }

    public function destroy(YouTubeVideo $youtubeVideo)
    {
        $youtubeVideo->delete();

        return response()->json([
            'success' => true,
            'message' => 'کلیپ یوتیوب حذف شد.',
        ]);
    }
}
