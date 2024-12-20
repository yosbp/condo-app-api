<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'unit_number',
        'condominium_id',
        'unit_type_id',
        'owner_name',
        'owner_phone',
        'owner_email',
        'balance',
        'type',
    ];

    /**
     * Get the condominium that owns the unit.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    /**
     * Get the owner that owns the unit.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
