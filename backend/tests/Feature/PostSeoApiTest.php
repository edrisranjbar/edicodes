<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSeoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_slug_endpoint_returns_meta_title_and_description(): void
    {
        $category = Category::create([
            'name' => 'توسعه وب',
            'slug' => 'web-development',
            'description' => 'مقالات توسعه وب',
        ]);

        $post = Post::create([
            'title' => 'آموزش لاراول',
            'slug' => 'laravel-tutorial',
            'summary' => 'خلاصه مقاله',
            'content' => '<p>محتوای نمونه</p>',
            'category_id' => $category->id,
            'published' => true,
            'published_at' => now(),
            'meta_title' => 'عنوان متا برای لاراول',
            'meta_description' => 'توضیح متا برای مقاله لاراول',
        ]);

        $response = $this->getJson('/api/posts/slug/' . $post->slug);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.meta_title', 'عنوان متا برای لاراول')
            ->assertJsonPath('data.meta_description', 'توضیح متا برای مقاله لاراول');
    }
}
