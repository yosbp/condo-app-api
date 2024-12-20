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
        'amount',
        'reserve_fund',
        'total_amount',
        'month',
        'year',
        'due_date',
    ];

    /**
     * Get the condominium that owns the invoice.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominium::class); // An invoice belongs to a condominium
    }

    /**
     * Get the invoice items for the invoice.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the expenses through invoice items.
     */
    public function expenses()
    {
        return $this->hasManyThrough(Expense::class, InvoiceItem::class, 'invoice_id', 'id', 'id', 'expense_id');
        // hasManyThrough(RelatedModel, ThroughModel, ForeignKeyOnThroughModel, ForeignKeyOnRelatedModel, LocalKey, LocalKeyOnThroughModel)
    }
}
