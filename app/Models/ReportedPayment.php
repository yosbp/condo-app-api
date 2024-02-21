<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedPayment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'owner_id',
        'amount',
        'bank',
        'description',
        'type',
        'is_verified',
        'date'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Get the owner that owns the reported payment.
     */
    public function owner() {
        return $this->belongsTo(Owner::class); // A reported payment belongs to an owner
    }
}
