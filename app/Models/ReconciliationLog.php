<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconciliationLog extends Model
{
    use HasFactory;

    protected $table = 'reconciliation_logs';

    protected $fillable = [
        'bank_code',
        'settlement_date',
        'file_source',
        'total_bank_records',
        'total_matched_records',
        'total_discrepancies',
        'total_amount_bank',
        'total_amount_siakad',
        'discrepancy_details',
        'status',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'total_amount_bank' => 'decimal:2',
        'total_amount_siakad' => 'decimal:2',
        'discrepancy_details' => 'array',
    ];
}
