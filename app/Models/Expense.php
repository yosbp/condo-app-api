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
        'expense_category_id',
        'description',
        'amount',
        'entry_date',
        'date',
        'invoiced'
    ];

    /**
     * Get the category that owns the expense.
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    
    /**
     * Get the condominium that owns the expense.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }
}
