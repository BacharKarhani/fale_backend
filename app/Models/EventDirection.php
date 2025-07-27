<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDirection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_tagline',
        'section_title',
        'description',
        'call_text',
        'call_number',
        'call_icon',
        'counters',
        'is_shown'
    ];

    protected $casts = [
        'counters' => 'array', // Decode JSON automatically
    ];
}
