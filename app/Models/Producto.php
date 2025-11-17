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
        'precio'         => 'decimal:2',
        'stock'          => 'integer',
    ];

    /* ========================================================
     * 🔹 RELACIONES
     * ======================================================== */

    /**
     * Vendedor dueño del producto (relación con User)
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Categoría a la que pertenece el producto
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Ítems de pedidos donde aparece este producto
     */
    public function pedidoItems(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'producto_id');
    }

    /**
     * 🔹 Reseñas asociadas al producto
     */
    public function reseñas(): HasMany
    {
        return $this->hasMany(Reseña::class, 'producto_id');
    }

    /* ========================================================
     * 🔹 MÉTODOS PERSONALIZADOS
     * ======================================================== */

    /**
     * Retorna el promedio de estrellas del producto
     */
    public function promedioReseñas(): ?float
    {
        return $this->reseñas()->avg('estrellas');
    }

    /**
     * Retorna el total de reseñas que tiene el producto
     */
    public function totalReseñas(): int
    {
        return $this->reseñas()->count();
    }

    /* ========================================================
     * 🔹 SCOPES
     * ======================================================== */

    /**
     * Scope para filtrar productos por vendedor
     */
    public function scopeDelVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }
}
