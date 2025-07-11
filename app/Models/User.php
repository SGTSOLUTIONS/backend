<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\Gender;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasapiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasapiTokens, HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'avatar',
    'address',
    'designation',
    'dob',
    'gender',
];


    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class,
         'gender' => Gender::class,
    ];

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }
    public function roles()
{
    return $this->belongsToMany(Role::class);
}

public function hasRole($role)
{
    if (is_string($role)) {
        return $this->roles->contains('name', $role);
    }

    return !!$role->intersect($this->roles)->count();
}

public function assignRole($role)
{
    if (is_string($role)) {
        $role = Role::where('name', $role)->firstOrFail();
    }

    $this->roles()->syncWithoutDetaching($role);
}


}
