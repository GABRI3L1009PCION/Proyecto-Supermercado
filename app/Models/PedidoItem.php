<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_items';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'vendor_id',
        'cantidad',
        'precio_unitario',
        'fulfillment_status',
        'delivery_mode',
        'vendor_zone_id',
        'delivery_fee',
        'repartidor_id',
        'justificacion',
        'pickup_contact',
        'pickup_phone',
        'pickup_address',
        'delivery_instructions',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'cantidad'        => 'integer',
        'delivery_fee'    => 'decimal:2',
        'pickup_contact'  => 'string',
        'pickup_phone'    => 'string',
        'pickup_address'  => 'string',
        'delivery_instructions' => 'string',
    ];

    // Estados
    const ESTADO_ACEPTADO    = 'accepted';
    const ESTADO_PREPARANDO  = 'preparing';
    const ESTADO_LISTO       = 'ready';
    const ESTADO_ENTREGADO   = 'delivered';
    const ESTADO_RECHAZADO   = 'rejected';
    const ESTADO_CANCELADO   = 'canceled';

    // Modos de entrega
    const DELIVERY_PENDING        = 'pending';
    const DELIVERY_VENDOR_SELF    = 'vendor_self';
    const DELIVERY_VENDOR_COURIER = 'vendor_courier';
    const DELIVERY_MARKET_COURIER = 'market_courier';

    public static function deliveryModeLabels(): array
    {
        return [
            self::DELIVERY_PENDING        => 'Pendiente de asignación',
            self::DELIVERY_VENDOR_SELF    => 'Entrega directa del vendedor',
            self::DELIVERY_VENDOR_COURIER => 'Repartidor asignado por vendedor',
            self::DELIVERY_MARKET_COURIER => 'Repartidor del supermercado',
        ];
    }

    /** Relaciones */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto(): BelongsTo
    {
        // withDefault evita errores si el producto fue borrado
        return $this->belongsTo(Producto::class, 'producto_id')->withDefault([
            'nombre' => 'Producto no disponible',
            'codigo' => null,
            'vendor_id' => null,
        ]);
    }

    /** Vendedor (users.id) propietario del ítem */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    public function vendorZone(): BelongsTo
    {
        return $this->belongsTo(VendorDeliveryZone::class, 'vendor_zone_id');
    }

    public function reseña(): HasOne
    {
        return $this->hasOne(Reseña::class, 'pedido_item_id');
    }

    /** Accesores */
    public function getTotalAttribute(): float
    {
        return (float) $this->cantidad * (float) $this->precio_unitario;
    }

    public function getNombreProductoAttribute(): string
    {
        return (string) ($this->producto->nombre ?? 'Producto no disponible');
    }

    public function getImagenProductoAttribute()
    {
        return $this->producto->imagen ?? null;
    }

    /** Scopes */
    public function scopeDelVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('fulfillment_status', $estado);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('fulfillment_status', [self::ESTADO_ACEPTADO, self::ESTADO_PREPARANDO]);
    }

    public function scopeCompletados($query)
    {
        return $query->where('fulfillment_status', self::ESTADO_ENTREGADO);
    }

    /** Métodos de ayuda */
    public function estaPendiente(): bool
    {
        return in_array($this->fulfillment_status, [self::ESTADO_ACEPTADO, self::ESTADO_PREPARANDO], true);
    }

    public function estaCompletado(): bool
    {
        return $this->fulfillment_status === self::ESTADO_ENTREGADO;
    }

    public function estaCancelado(): bool
    {
        return $this->fulfillment_status === self::ESTADO_CANCELADO;
    }

    public function marcarComoPreparando(): bool
    {
        $this->fulfillment_status = self::ESTADO_PREPARANDO;
        return $this->save();
    }

    public function marcarComoListo(): bool
    {
        $this->fulfillment_status = self::ESTADO_LISTO;
        return $this->save();
    }

    public function marcarComoEntregado(): bool
    {
        $this->fulfillment_status = self::ESTADO_ENTREGADO;
        return $this->save();
    }

    public function marcarComoCancelado(): bool
    {
        $this->fulfillment_status = self::ESTADO_CANCELADO;
        return $this->save();
    }

    /** Relleno automático de vendor_id/precio al crear */
    protected static function booted(): void
    {
        static::creating(function (PedidoItem $item) {
            // Si no vino vendor_id, tomarlo del producto
            if (empty($item->vendor_id) && $item->producto) {
                $item->vendor_id = $item->producto->vendor_id;
            }
            // Si no vino precio_unitario, usar el precio del producto
            if (empty($item->precio_unitario) && $item->producto) {
                $item->precio_unitario = $item->producto->precio;
            }
            if (empty($item->delivery_mode)) {
                $item->delivery_mode = self::DELIVERY_PENDING;
            }
            if ($item->delivery_fee === null) {
                $item->delivery_fee = 0;
            }
        });
    }
}
