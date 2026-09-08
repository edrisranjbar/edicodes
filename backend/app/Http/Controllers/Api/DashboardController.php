<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PageView;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get counts
        $postsCount = Post::count();
        $categoriesCount = Category::count();
        $pendingCommentsCount = Comment::pending()->count();
        $publishedPostsCount = Post::published()->count();
        $donationsCount = Donation::where('status', 'paid')->count();
        $totalDonations = Donation::where('status', 'paid')->sum('amount');
        
        // Get page view statistics
        $totalPageViews = PageView::count();
        $totalUniqueVisitors = PageView::uniqueVisitorsCount();
        
        // Today's views
        $todayViews = PageView::forDate(Carbon::today())->count();
        $todayUniqueVisitors = PageView::forDate(Carbon::today())->uniqueVisitorsCount();
        
        // This week's views
        $weekViews = PageView::forPeriod(Carbon::now()->subDays(7), Carbon::now())->count();
        $weekUniqueVisitors = PageView::forPeriod(Carbon::now()->subDays(7), Carbon::now())->uniqueVisitorsCount();
        
        // Get 3 most recent posts with their categories
        $recentPosts = Post::with('category')->withCount('pageViews')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        // Get top 5 most viewed posts
        $topViewedPosts = Post::select('posts.*')
            ->withCount('pageViews')
            ->orderByDesc('page_views_count')
            ->take(5)
            ->with('category')
            ->get();

        // Return statistics and recent posts
        return response()->json([
            'stats' => [
                'posts' => $postsCount,
                'categories' => $categoriesCount,
                'pendingComments' => $pendingCommentsCount,
                'publishedCount' => $publishedPostsCount,
                'views' => [
                    'total' => $totalPageViews,
                    'unique' => $totalUniqueVisitors,
                    'today' => $todayViews,
                    'todayUnique' => $todayUniqueVisitors,
                    'week' => $weekViews,
                    'weekUnique' => $weekUniqueVisitors,
                ],
                'donations' => $donationsCount,
                'totalDonations' => $totalDonations,
            ],
            'recentPosts' => $recentPosts,
            'topViewedPosts' => $topViewedPosts
        ]);
    }
}
