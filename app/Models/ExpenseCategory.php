<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'condominium_id',
        'name',
        'description',
    ];

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id', 'id');
    }
}
