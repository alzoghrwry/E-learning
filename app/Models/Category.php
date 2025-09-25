<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- لازم تضيف هذا
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory; // <--- هذا ممكن يعمل factory

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
    ];

    // علاقة Category → كورسات
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
