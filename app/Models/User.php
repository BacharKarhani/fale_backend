<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Symfony\Component\HttpKernel\Bundle\Bundle;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'dob',
        'gender',
        'company_name',
        'specialization',
        'geographical_scope',
        'code',
        'role_id',
        'status',
        'photo', // ✅ Add this line
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'specialization' => 'array',
];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'status' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

 // In App\Models\User
public function isAdmin(): bool
{
    // works with either role name or role_id
    $roleName = strtolower($this->role->name ?? '');
    $roleId   = (int) ($this->role_id ?? 0);

    return in_array($roleName, ['admin', 'subadmin'], true)
        || in_array($roleId, [1, 5], true); // adjust IDs if yours differ
}


    public function boothApplications()
{
    return $this->hasMany(BoothApplication::class);
}

public function bundles()
{
    return $this->belongsToMany(\App\Models\Bundle::class)->withPivot('status')->withTimestamps();
}

}
