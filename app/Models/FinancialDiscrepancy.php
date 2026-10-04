<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialDiscrepancy extends Model
{
    use HasFactory;

    protected $table = 'financial_discrepancies';

    protected $fillable = [
        'id_reconciliation_log',
        'bank_code',
        'nomor_transaksi_bank',
        'nomor_va',
        'amount_bank',
        'amount_siakad',
        'discrepancy_amount',
        'anomaly_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount_bank' => 'decimal:2',
        'amount_siakad' => 'decimal:2',
        'discrepancy_amount' => 'decimal:2',
    ];

    public function reconciliationLog(): BelongsTo
    {
        return $this->belongsTo(ReconciliationLog::class, 'id_reconciliation_log');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'OPEN');
    }

    public function scopeInvestigating($query)
    {
        return $query->where('status', 'INVESTIGATING');
    }
}
