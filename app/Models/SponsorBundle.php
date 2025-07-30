<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorBundle extends Model
{
    protected $fillable = ['type', 'price', 'description', 'benefits'];

    protected $casts = [
        'benefits' => 'array',
    ];
}
