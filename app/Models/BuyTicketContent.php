<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyTicketContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'timing',
        'title',
        'description',
        'image',
        'is_shown',
    ];
}
