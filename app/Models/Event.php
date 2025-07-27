<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_id',
        'title',
        'description',
        'image',
        'time',
        'address',
        'is_shown',
    ];

    public function day()
    {
        return $this->belongsTo(Day::class);
    }
}