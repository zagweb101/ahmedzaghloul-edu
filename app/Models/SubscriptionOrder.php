<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SubscriptionOrder extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'reference',
        'amount_cents',
        'currency',
        'status',
        'payment_driver',
        'gateway_charge_id',
        'checkout_url',
        'customer_note',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public static function generateReference(): string
    {
        return 'ORD-' . strtoupper(Str::random(8));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function formattedAmount(): string
    {
        return number_format($this->amount_cents / 100) . ' ' . $this->currency;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
