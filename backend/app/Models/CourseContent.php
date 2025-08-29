<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'content',
        'video_path',
        'video_url',
        'video_duration',
        'is_free',
        'order',
        'duration'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'order' => 'integer',
        'video_duration' => 'integer'
    ];

    /**
     * Get the course that owns the content
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the video URL attribute
     */
    public function getVideoUrlAttribute()
    {
        if ($this->video_path) {
            return asset('storage/' . $this->video_path);
        }
        return null;
    }
}
