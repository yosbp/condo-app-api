<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condominium extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'condominiums';

    protected $fillable = [
        'administrator_id',
        'name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'name_to_invoice'
    ];

    /**
     * Obtener el administrador asociado al condominio.
     */
    public function administrator()
    {
        return $this->belongsTo(Administrator::class); // A condominium belongs to an administrator
    }

    /**
     * Obtener las unidades asociadas al condominio.
     */
    public function units()
    {
        return $this->hasMany(Unit::class); // A condominium has many units
    }

    /**
     * Obtener los balances asociados al condominio.
     */
    public function balances()
    {
        return $this->hasMany(Balance::class); // A condominium has many balances
    }
}
