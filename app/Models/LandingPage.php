<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'title',
        'description', 
        'date_range',
        'image',
        'stats_section_title',
        'exhibitors_count',
        'visitors_count',
        'panels_count',
        'stats_enabled'
    ];

    protected $casts = [
        'description' => 'array', // ✅ new line added
        'stats_enabled' => 'boolean',
        'exhibitors_count' => 'integer',
        'visitors_count' => 'integer',
        'panels_count' => 'integer'
    ];

    protected $attributes = [
        'stats_section_title' => 'Connect & Collaborate',
        'exhibitors_count' => 120,
        'visitors_count' => 15000,
        'panels_count' => 12,
        'stats_enabled' => true
    ];
}
