<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Obtener el usuario asociado al administrador.
     */
    public function user()
    {
        return $this->belongsTo(User::class); // An administrator belongs to a user
    }

    /**
     * Obtener el condominio asociado al administrador.
     */
    public function condominium()
    {
        return $this->hasOne(Condominium::class); // An administrator has one condominium
    }
}
