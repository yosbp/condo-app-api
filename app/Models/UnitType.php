<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'condominium_id',
        'description',
        'percentage',
    ];

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
