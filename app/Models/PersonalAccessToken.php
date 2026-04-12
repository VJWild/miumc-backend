<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class PersonalAccessToken extends SanctumToken
{
    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'ip_address',
        'user_agent',
        'token',
        'abilities'
    ];

    protected $hidden = [
        'token'
    ];

    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function tokenable()
    {
        return $this->morphTo();
    }
}
