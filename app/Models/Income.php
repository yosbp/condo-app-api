<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'condominium_id',
        'unit_id', 
        'description',
        'method',
        'bank',
        'amount',
        'date',
    ];
}
