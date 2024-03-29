<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the administrator that owns the user.
     */
    public function administrator()
    {
        return $this->hasOne(Administrator::class); // A user has one administrator
    }

    /**
     * Get the condominium that owns the user.
     */
    public function condominium()
    {
        return $this->hasOneThrough(Condominium::class, Administrator::class); // A user has one condominium through administrator
    }

    /**
     * Get the owner that owns the user.
     */
    public function owner()
    {
        return $this->hasOne(Owner::class); // A user has one Owner
    }
}
