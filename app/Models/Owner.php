<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'condominium_id',
        'unit_id',
        'is_verified'
    ];

    // cast is_verified to boolean
    protected $casts = [
        'is_verified' => 'boolean'
    ];

    /**
     * Get the user that owns the owner.
     */
    public function user() {
        return $this->belongsTo(User::class); // An owner belongs to a user
    }

    /**
     * Get the unit that owns the owner.
     */
    public function unit() {
        return $this->hasOne(Unit::class); // An owner belongs to a unit
    }

    /**
     * Get the reported payments for the owner.
     */
    public function reportedPayments() {
        return $this->hasMany(ReportedPayment::class); // An owner has many reported payments
    }
}
