<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'order',
    
    ];

    // Accessor لتحويل resource أو video_url إلى URL كامل
    protected $appends = ['resource_url', 'video_full_url'];

    public function getResourceUrlAttribute()
    {
        return $this->resource ? asset('storage/' . $this->resource) : null;
    }

    public function getVideoFullUrlAttribute()
    {
        return $this->video_url ? asset('storage/' . $this->video_url) : null;
    }

    // علاقة Lesson → Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
