<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmployee extends Model
{
    use HasFactory;

    protected $table = 'admin_employees';

    protected $fillable = [
        'name',
        'email',
        'gender',
        'dob',
        'phone_number',
        'qr_code',
        'company',    // <— جديد

    ];

    protected $casts = [
        'dob' => 'date',
    ];
}
