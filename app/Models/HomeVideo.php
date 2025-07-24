<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_file',
        'thumbnail',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
