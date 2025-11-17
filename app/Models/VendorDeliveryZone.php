<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDeliveryZone extends Model
{
    protected $fillable = [
        'vendor_id',
        'nombre',
        'coverage',
        'delivery_fee',
        'activo',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'activo'       => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
