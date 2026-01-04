<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'location',
        'branchname',
        'mobile',
        'company',
        'arabic_name',
        'address',
        'tr_no',
        'email',
        'vat_number',
        'commercial_registration_number',
        'commercial_registration_number_ar',
        'branch_number',
        'po_box',
        'file',
        'logo',
        'color',
        'sunmilogo',
        'receiptlogo',
        'pdflogo',
        'a5pdflogo',
        'currency',
        'transaction',
        'name',
        'supplier_building',
        'supplier_postal',
        'supplier_country',
        'invoice_customization_id',
        'invoice_profile_id',
        'invoice_transaction_code',

        // ✅ make sure these are fillable
        'last_invoice_counter',
        'last_invoice_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeLocationNameById($query, $branchId)
    {
        return $query->where('id', $branchId)->value('location');
    }

    /**
     * ✅ Atomically get the next invoice counter for this branch
     */
    public function getNextInvoiceCounter(): int
    {
        return DB::transaction(function () {
            // Lock row for update to prevent race conditions
            $branch = DB::table($this->getTable())
                ->where('id', $this->id)
                ->lockForUpdate()
                ->first();

            $current = (int) ($branch->last_invoice_counter ?? 0);
            $next = $current + 1;

            DB::table($this->getTable())
                ->where('id', $this->id)
                ->update([
                    'last_invoice_counter' => $next,
                    'updated_at' => now()
                ]);

            $this->last_invoice_counter = $next;

            return $next;
        }, 5);
    }
}
