<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_tagline',
        'section_title',
        'icon_1_class',
        'icon_1_subtitle',
        'icon_1_subtagline',
        'icon_2_class',
        'icon_2_subtitle',
        'icon_2_subtagline',
        'event_image'
    ];
}
