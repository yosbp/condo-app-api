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
        'name_to_invoice',
        'image_url'
    ];

    /**
     * Get the administrator that owns the condominium.
     */
    public function administrator()
    {
        return $this->belongsTo(Administrator::class); // A condominium belongs to an administrator
    }

    /**
     * Get the units for the condominium.
     */
    public function units()
    {
        return $this->hasMany(Unit::class); // A condominium has many units
    }

    /** 
     * Get units types for the condominium.
    */

    public function unitTypes()
    {
        return $this->hasMany(UnitType::class); // A condominium has many unit types
    }

    /**
     * Get the balances for the condominium.
     */
    public function balances()
    {
        return $this->hasMany(Balance::class); // A condominium has many balances
    }

    /**
     * Get the owners for the condominium.
     */
    public function owners()
    {
        return $this->hasMany(Owner::class); // A condominium has many owners
    }

    /**
     * Get Expenses Categories for the condominium.
     */
    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class); // A condominium has many expense categories
    }

    /**
     * Get the expenses for the condominium.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class); // A condominium has many expenses
    }
}
