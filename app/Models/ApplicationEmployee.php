<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationEmployee extends Model
{
    use HasFactory;

protected $fillable = [
    'booth_application_id',
    'name',
    'email',
    'gender',
    'dob',
    'phone_number',
    'qr_code',
];


    public function application()
    {
        return $this->belongsTo(BoothApplication::class, 'booth_application_id');
    }
}
