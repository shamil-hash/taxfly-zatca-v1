<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalTransaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'reference',
        'narration'
    ];

    /**
     * Get the journal entries for the transaction.
     */
    public function entries()
    {
        // explicitly specify foreign key
        return $this->hasMany(JournalEntry::class, 'transaction_id');
    }
}
