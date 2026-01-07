<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with('category');
        
        // Check if this is an admin request (authenticated) or public request
        if ($request->user()) {
            // Admin request - show all posts unless status is specified
            if ($request->has('status') && $request->status === 'published') {
                $query->published();
            }
        } else {
            // Public request - always show only published posts
            $query->published();
        }
        
        $query->latest('published_at');
        
        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // Filter by search term
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('summary', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }
        
        // Paginate results
        $posts = $query->paginate(10);
        
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts',
            'summary' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set published date if post is published and date not provided
        if (($validated['published'] ?? true) && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);
        
        // Regenerate sitemap after creating a post
        $this->regenerateSitemap();
        
        return new PostResource($post->load('category'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post)
    {
        // For public requests, only allow access to published posts
        if (!$request->user() && !$post->published) {
            abort(404);
        }
        
        // Record the page view
        $post->recordView($request);
        
        return new PostResource($post->load('category'));
    }

    /**
     * Find post by slug
     */
    public function findBySlug(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with('category')
            ->firstOrFail();
            
        // Record the page view
        $post->recordView($request);
            
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'summary' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'image' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Generate slug if title was updated but slug wasn't provided
        if (isset($validated['title']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set published date if post is newly published
        if (isset($validated['published']) && $validated['published'] === true && 
            !$post->published && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post->update($validated);
        
        // Regenerate sitemap after updating a post
        $this->regenerateSitemap();
        
        return new PostResource($post->load('category'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        
        // Regenerate sitemap after deleting a post
        $this->regenerateSitemap();
        
        return response()->json(['message' => 'Post deleted successfully']);
    }
    
    /**
     * Regenerate sitemap in the background
     */
    private function regenerateSitemap()
    {
        // Run sitemap generation in the background to avoid blocking the response
        try {
            // Try to queue it first (if queue worker is running)
            if (config('queue.default') !== 'sync') {
                Artisan::queue('sitemap:generate');
            } else {
                // If sync queue, run it in background using exec
                $artisanPath = base_path('artisan');
                $command = PHP_OS_FAMILY === 'Windows' 
                    ? "start /B php \"{$artisanPath}\" sitemap:generate > NUL 2>&1"
                    : "php \"{$artisanPath}\" sitemap:generate > /dev/null 2>&1 &";
                exec($command);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to regenerate sitemap: ' . $e->getMessage());
        }
    }
}
