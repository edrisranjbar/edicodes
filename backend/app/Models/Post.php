<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'meta_title',
        'meta_description',
        'content',
        'image',
        'category_id',
        'published',
        'published_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the approved root comments for the post.
     */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->with(['replies' => function($query) {
                $query->where('status', 'approved');
            }]);
    }

    /**
     * Get the page views for the post.
     */
    public function pageViews()
    {
        return $this->morphMany(PageView::class, 'viewable', 'page_type', 'page_id');
    }

    /**
     * Get total view count for the post.
     */
    public function getViewsCountAttribute()
    {
        return $this->pageViews()->count();
    }

    /**
     * Get unique visitors count for the post.
     */
    public function getUniqueVisitorsCountAttribute()
    {
        return $this->pageViews()->uniqueVisitorsCount();
    }

    /**
     * Record a view for this post.
     */
    public function recordView($request)
    {
        return PageView::createView($this, $request);
    }
}
