<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'transaction_id',
        'entry_date',
        'account',
        'description',
        'debit',
        'credit',
        'entity',
        'paid_through',
        'reference',
        'admin_id',
        'branch_id',
        'created_by',
        'source_table',
        'source_id'
    ];

    /**
     * Get the transaction that owns the journal entry.
     */
    public function transaction()
    {
        // explicitly specify foreign key
        return $this->belongsTo(JournalTransaction::class, 'transaction_id');
    }
}
