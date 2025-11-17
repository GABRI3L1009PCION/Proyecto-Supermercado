<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDeliveryZone extends Model
{
    protected $fillable = [
        'seller_id',
        'nombre',
        'descripcion_cobertura',
        'tarifa_reparto',
        'activa',
    ];

    protected $casts = [
        'tarifa_reparto' => 'decimal:2',
        'activa'         => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'seller_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
