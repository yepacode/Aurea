<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NpsResponse extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'score',
        'comment',
        'token',
        'sent_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'score'        => 'integer',
            'sent_at'      => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * NPS bucket: promoter / passive / detractor / null (sin responder).
     */
    public function bucket(): ?string
    {
        if ($this->score === null) {
            return null;
        }
        if ($this->score >= 9) return 'promoter';
        if ($this->score >= 7) return 'passive';
        return 'detractor';
    }

    public function hasResponded(): bool
    {
        return $this->responded_at !== null;
    }
}
