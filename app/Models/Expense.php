<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'condominium_id',
        'category_id',
        'description',
        'amount',
        'entry_date',
        'date'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }
}
