<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporterBill extends Model
{
    protected $table = 'reporter_bills';

    protected $fillable = [
        'portal_id',
        'reporter_id',
        'billing_month',
        'total_articles',
        'rate_per_article',
        'bonus_amount',
        'deduction_amount',
        'total_payable',
        'payment_method',
        'account_number',
        'transaction_id',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'rate_per_article' => 'decimal:2',
        'bonus_amount'     => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'total_payable'    => 'decimal:2',
        'paid_at'          => 'datetime',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
