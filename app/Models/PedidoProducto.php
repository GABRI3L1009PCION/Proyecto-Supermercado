<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoProducto extends Model
{
    use HasFactory;

    protected $table = 'pedido_productos';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        // nuevos:
        'vendor_id',
        'fulfillment_status',
    ];

    // Relaciones existentes
    public function pedido()  { return $this->belongsTo(Pedido::class); }
    public function producto(){ return $this->belongsTo(Producto::class); }

    // Nuevo: dueño del ítem (vendedor)
    public function vendor()  { return $this->belongsTo(Vendor::class, 'vendor_id'); }

    // Atributo total (cantidad * precio_unitario)
    public function getTotalAttribute()
    {
        return (float) ($this->cantidad * $this->precio_unitario);
    }

    // Scopes útiles
    public function scopeDelVendor($q, $vendorId) { return $q->where('vendor_id', $vendorId); }
    public function scopePendientes($q) { return $q->whereIn('fulfillment_status', ['requested','accepted','preparing']); }
}
