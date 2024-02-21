<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'condominium_id',
        'description',
        'status'
    ];

    /**
     * Get the owner that owns the maintenance record.
     */
    public function owner() {
        return $this->belongsTo(Owner::class); // A maintenance record belongs to an owner
    }
}
