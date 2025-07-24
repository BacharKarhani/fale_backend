<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_title',
        'date',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}