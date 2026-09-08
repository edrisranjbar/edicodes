<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageViewController extends Controller
{
    /**
     * Record a page view for a post
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\Response
     */
    public function recordPostView(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $post->recordView($request);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Record a page view for a generic route (/, /blog, etc.)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordGenericPageView(Request $request)
    {
        $path = $request->input('path');
        
        if (empty($path)) {
            return response()->json(['error' => 'Path is required'], 400);
        }
        
        PageView::createGenericPageView($path, $request);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get analytics for all posts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAnalytics(Request $request)
    {
        $period = $request->input('period', 'week');
        $startDate = null;
        $endDate = Carbon::now();
        
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::today()->subSecond();
                break;
            case 'week':
                $startDate = Carbon::today()->subDays(6);
                break;
            case 'month':
                $startDate = Carbon::today()->subDays(29);
                break;
            case 'year':
                $startDate = Carbon::today()->subDays(364);
                break;
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today()->subDays(6);
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
                break;
            default:
                $startDate = Carbon::today()->subDays(6);
        }
        
        // Total views
        $totalViews = PageView::forPeriod($startDate, $endDate)->count();
        
        // Unique visitors
        $uniqueVisitors = PageView::forPeriod($startDate, $endDate)->distinct()->count('ip_address');
        
        // Daily views for chart
        $dailyViews = PageView::select(
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as views'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
            )
            ->forPeriod($startDate, $endDate)
            ->groupBy(DB::raw('DATE(viewed_at)'))
            ->orderBy('date')
            ->get();
            
        // Most viewed posts
        $topPosts = Post::select(
                'posts.*', 
                DB::raw('COUNT(page_views.id) as views_count'),
                DB::raw('COUNT(DISTINCT page_views.ip_address) as unique_visitors_count')
            )
            ->join('page_views', function($join) {
                $join->on('posts.id', '=', 'page_views.page_id')
                    ->where('page_views.page_type', '=', Post::class);
            })
            ->whereBetween('page_views.viewed_at', [$startDate, $endDate])
            ->groupBy('posts.id')
            ->orderByDesc('views_count')
            ->take(5)
            ->with('category')
            ->get();
            
        // Most viewed pages (generic routes like '/' and '/blog')
        $topPages = PageView::select(
                'referrer as path', 
                DB::raw('COUNT(*) as views_count'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
            )
            ->where('page_type', '=', 'route')
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->groupBy('referrer')
            ->orderByDesc('views_count')
            ->take(5)
            ->get();
        
        return response()->json([
            'totalViews' => $totalViews,
            'uniqueVisitors' => $uniqueVisitors,
            'dailyViews' => $dailyViews,
            // Post accessors calculate lifetime counts; preserve the period aggregates.
            'topPosts' => $topPosts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'category' => $post->category,
                    'views_count' => (int) $post->getRawOriginal('views_count'),
                    'unique_visitors_count' => (int) $post->getRawOriginal('unique_visitors_count'),
                ];
            }),
            'topPages' => $topPages,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ]
        ]);
    }
    
    /**
     * Get analytics for a specific post
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\Response
     */
    public function getPostAnalytics(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $period = $request->input('period', 'week');
        $startDate = null;
        $endDate = Carbon::now();
        
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->subDays(7);
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'all':
                $startDate = Carbon::create(2000, 1, 1);
                break;
            default:
                $startDate = Carbon::now()->subDays(7);
        }
        
        // Total views for this post
        $totalViews = $post->pageViews()->forPeriod($startDate, $endDate)->count();
        
        // Unique visitors for this post
        $uniqueVisitors = $post->pageViews()->forPeriod($startDate, $endDate)->uniqueVisitorsCount();
        
        // Daily views for chart
        $dailyViews = $post->pageViews()
            ->select(
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as views'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
            )
            ->forPeriod($startDate, $endDate)
            ->groupBy(DB::raw('DATE(viewed_at)'))
            ->orderBy('date')
            ->get();
        
        return response()->json([
            'post' => $post->only('id', 'title', 'slug'),
            'totalViews' => $totalViews,
            'uniqueVisitors' => $uniqueVisitors,
            'dailyViews' => $dailyViews,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ]
        ]);
    }
}
