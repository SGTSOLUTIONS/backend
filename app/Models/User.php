<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\GuestOnly;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class, // Casts 'role' to enum
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Helper: check if user is admin
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    // ✅ Helper: check if user is normal user
    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }
}
