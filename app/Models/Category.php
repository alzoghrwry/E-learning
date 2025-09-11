<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

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

