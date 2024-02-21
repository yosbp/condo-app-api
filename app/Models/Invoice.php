<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'condominium_id',
        'description',
        'total_amount',
        'month',
        'year',
        'due_date',
        'issue_date',
    ];

    /**
     * Get the condominium that owns the invoice.
     */
    public function condominium() {
        return $this->belongsTo(Condominium::class); // An invoice belongs to a condominium
    }

    /**
     * Get expenses that belong to the invoice.
     */
    public function expenses() {
        return $this->hasMany(Expense::class, 'invoice_items'); // An invoice has many expenses
    }
}
