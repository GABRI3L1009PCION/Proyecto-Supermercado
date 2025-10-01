<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'vendor_id',
        'nombre',
        'slug',
        'precio',
        'stock',
        'status',
        'estado',
        'descripcion',
        'categoria_id',
        'imagen',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock'  => 'integer',
    ];

    /** Vendedor dueño del producto (users.id) */
    public function vendor(): BelongsTo
    {
        // Ojo: usamos User, no Vendor. La FK es vendor_id.
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /** Ítems de pedidos donde aparece este producto (comodidad) */
    public function pedidoItems(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'producto_id');
    }

    /** Scope para filtrar por vendedor */
    public function scopeDelVendor($q, $vendorId)
    {
        return $q->where('vendor_id', $vendorId);
    }
}
