<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'reset_token',
        'reset_expires_at',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    protected function casts(): array
    {
        return [
            'reset_expires_at' => 'datetime',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }
}