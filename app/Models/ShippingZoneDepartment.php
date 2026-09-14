<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingZoneDepartment extends Model
{
    protected $fillable = ['shipping_zone_id', 'department'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}
