<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'role',
        'image',
        'status',
        'email_verified_at',
    ];


    protected $hidden = [
        'remember_token',
    ];
    public function getJWTIdentifier()
    {
        return $this->user_id;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}