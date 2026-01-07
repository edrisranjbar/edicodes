<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('posts')->get();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories',
        ]);

        // Generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category = Category::create($validated);
        
        // Regenerate sitemap after creating a category
        $this->regenerateSitemap();
        
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->loadCount('posts');
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        // Generate slug if name was updated but slug wasn't provided
        if (isset($validated['name']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);
        
        // Regenerate sitemap after updating a category
        $this->regenerateSitemap();
        
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        
        // Regenerate sitemap after deleting a category
        $this->regenerateSitemap();
        
        return response()->json(['message' => 'Category deleted successfully']);
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
