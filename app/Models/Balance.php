<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'condominium_id',
        'income_id',
        'expense_id',
        'balance',
    ];

    /**
     * Get the condominium that owns the balance.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    /**
     * Get the income that owns the balance.
     */
    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    /**
     * Get the expense that owns the balance.
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
