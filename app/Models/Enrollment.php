<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    public $timestamps = false;
    protected $fillable = [
        'course_id',
        'student_id',
        'enrolled_at',
    ];

    protected $dates = ['enrolled_at', 'created_at', 'updated_at'];

    // علاقة الطالب
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // علاقة الكورس
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
