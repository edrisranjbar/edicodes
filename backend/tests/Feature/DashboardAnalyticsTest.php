<?php
namespace Tests\Feature;

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PageViewController;
use App\Models\Post;
use App\Models\PageView;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_counts_and_analytics_respect_the_selected_period(): void
    {
        Carbon::setTestNow('2026-09-08 12:00:00');
        try {
            $category = \App\Models\Category::create(['name'=>'Test','slug'=>'test']);
            $post = Post::create(['category_id'=>$category->id,'summary'=>'Summary','title'=>'Example','slug'=>'example','content'=>'Content','published'=>true]);
            foreach ([['2026-09-02 00:00:00','192.0.2.1'], ['2026-09-08 10:00:00','192.0.2.1'], ['2026-09-08 11:00:00','192.0.2.2'], ['2026-09-01 23:59:59','192.0.2.3']] as [$time,$ip]) {
                PageView::create(['page_type'=>Post::class,'page_id'=>$post->id,'ip_address'=>$ip,'user_agent'=>'test','viewed_at'=>$time]);
            }
            $dashboard = app(DashboardController::class)->index()->getData(true);
            $this->assertSame(4, $dashboard['recentPosts'][0]['page_views_count']);
            $data = app(PageViewController::class)->getAnalytics(Request::create('/', 'GET', ['period'=>'week']))->getData(true);
            $this->assertSame('2026-09-02', $data['period']['start']);
            $this->assertSame(3, $data['totalViews']);
            $this->assertSame(2, $data['uniqueVisitors']);
            $this->assertSame(3, $data['topPosts'][0]['views_count']);
            $this->assertSame(2, $data['topPosts'][0]['unique_visitors_count']);
            $this->assertCount(2, $data['dailyViews']);
        } finally { Carbon::setTestNow(); }
    }
}

