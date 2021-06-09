<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    const PER_PAGE =10;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'client_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function role() {
        return $this->belongsTo(Role::class,'role_id');
    }
    public function client() {
        return $this->belongsTo(Client::class,'client_id');
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
