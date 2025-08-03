<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoothArea extends Model
{
    use HasFactory;

protected $fillable = [
    'label',
    'dimensions',
    'price',
    'benefits',
    'ticket_number', // <-- Add this
];


    public function slots()
    {
        return $this->hasMany(BoothAreaSlot::class, 'area_id');
    }

    public function applications()
    {
        return $this->hasMany(BoothApplication::class, 'area_id');
    }
}
