<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class YouTubeController extends Controller
{
    public function index(Request $request)
    {
        $channelId = env('YOUTUBE_CHANNEL_ID');
        $max = (int) env('YOUTUBE_MAX_VIDEOS', 6);

        if (empty($channelId)) {
            return response()->json(['videos' => []]);
        }

        $cacheKey = "youtube.latest.videos.{$channelId}.{$max}";

        $videos = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($channelId, $max) {
            try {
                if (preg_match('/^UC[a-zA-Z0-9_-]{20,}$/', $channelId)) {
                    $feedUrl = "https://www.youtube.com/feeds/videos.xml?channel_id={$channelId}";
                } else {
                    $feedUrl = "https://www.youtube.com/feeds/videos.xml?user={$channelId}";
                }

                $res = Http::timeout(5)->get($feedUrl);
                if (! $res->ok()) return [];

                $xml = @simplexml_load_string($res->body());
                if (! $xml) return [];

                $items = [];
                foreach ($xml->entry as $entry) {
                    $videoId = (string) $entry->children('yt', true)->videoId;
                    $title = (string) $entry->title;
                    $link = "https://www.youtube.com/watch?v={$videoId}";
                    $thumbnail = "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg";

                    $items[] = [
                        'id' => $videoId,
                        'title' => $title,
                        'url' => $link,
                        'thumbnail' => $thumbnail,
                    ];

                    if (count($items) >= $max) break;
                }

                return $items;
            } catch (\Throwable $e) {
                Log::warning('YouTube feed fetch failed: ' . $e->getMessage());
                return [];
            }
        });

        return response()->json(['videos' => $videos]);
    }
}
