<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPlan extends Model
{
    protected $fillable = ['plan_name', 'price', 'features'];

    protected $casts = [
        'features' => 'array',
    ];
}