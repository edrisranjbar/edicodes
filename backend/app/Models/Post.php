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
        'status',
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
        return $query->where(function($q) {
            $q->where('published', true)
              ->orWhere(function($q2) {
                  $q2->where('status', 'published')
                     ->orWhere(function($q3) {
                         $q3->where('status', 'scheduled')
                            ->where('published_at', '<=', now());
                     });
              });
        });
    }

    /**
     * Determine if the post is published (including scheduled posts whose time has arrived).
     */
    public function isPublished()
    {
        if ($this->published) return true;
        if ($this->status === 'published') return true;
        if ($this->status === 'scheduled' && $this->published_at && $this->published_at->lte(now())) return true;
        return false;
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
