<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Support\Facades\Artisan;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts whose publish time has arrived';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled posts to publish...');

        $posts = Post::where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts ready to publish.');
            return 0;
        }

        foreach ($posts as $post) {
            $post->status = 'published';
            // keep backward compatibility
            $post->published = true;
            // ensure published_at is set
            if (!$post->published_at) {
                $post->published_at = now();
            }
            $post->save();
            $this->info("Published post id={$post->id} slug={$post->slug}");
        }

        // Regenerate sitemap after publishing
        try {
            Artisan::call('sitemap:generate');
            $this->info('Sitemap regeneration queued.');
        } catch (\Exception $e) {
            $this->error('Failed to regenerate sitemap: ' . $e->getMessage());
        }

        return 0;
    }
}
