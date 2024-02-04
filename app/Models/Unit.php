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
        'owner_name',
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
}
