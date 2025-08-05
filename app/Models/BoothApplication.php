<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoothApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'area_id',
        'slot_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(BoothArea::class, 'area_id');
    }

    public function slot()
    {
        return $this->belongsTo(BoothAreaSlot::class, 'slot_id');
    }
    public function employees()
{
    return $this->hasMany(ApplicationEmployee::class, 'booth_application_id');
}


}
