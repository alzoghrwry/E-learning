<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date',
        'file', // لإضافة رفع ملفات الواجبات
    ];

    protected $dates = ['due_date', 'created_at', 'updated_at'];

    // Accessor لتحويل الملف إلى URL كامل
    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    // علاقة Assignment → Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // علاقة Assignment → Submissions
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
