<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoothAreaSlot extends Model
{
    protected $fillable = [
        'area_id',
        'user_id',
        'start_time',
        'end_time',
        'is_reserved',
        'status',
    ];

    public function area()
    {
        return $this->belongsTo(BoothArea::class, 'area_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
