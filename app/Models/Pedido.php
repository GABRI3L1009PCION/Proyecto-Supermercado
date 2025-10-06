<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'repartidor_id',
        'subtotal',
        'descuento',
        'envio',
        'total',
        'metodo_pago',
        'estado_pago',
        'estado_global',
        'direccion_envio',
        'facturacion',
        'codigo', // Agregado para el código del pedido
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'envio' => 'decimal:2',
        'total' => 'decimal:2',
        'direccion_envio' => 'array',
        'facturacion' => 'array',
    ];

    // Relación con el cliente (User)
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el repartidor (User)
    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    // Relación con los items del pedido
    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function itemsSupermercado()
    {
        return $this->items()->whereNull('vendor_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'pedido_items')
            ->withPivot('cantidad', 'precio_unitario', 'vendor_id');
    }

    public function productosSupermercado()
    {
        return $this->productos()->wherePivotNull('vendor_id');
    }

    // Relación con los pagos
    public function pagos()
    {
        return $this->hasMany(Payment::class, 'pedido_id');
    }

    // Scope para pedidos pagados
    public function scopePagados($q)
    {
        return $q->where('estado_pago', 'pagado');
    }

    // Scope para pedidos pendientes
    public function scopePendientes($q)
    {
        return $q->where('estado_pago', 'pendiente');
    }

    // Accesor para el código del pedido
    public function getCodigoAttribute()
    {
        return $this->attributes['codigo'] ?? 'PED-' . $this->id;
    }

    // Método para verificar si el pedido pertenece al usuario autenticado
    public function perteneceAlUsuario($userId)
    {
        return $this->user_id == $userId;
    }

    // Método para obtener la dirección formateada
    public function getDireccionFormateadaAttribute()
    {
        if (!$this->direccion_envio || !is_array($this->direccion_envio)) {
            return 'Dirección no especificada';
        }

        $dir = $this->direccion_envio;
        $direccion = '';

        $componentes = [];
        $descripcion = trim((string) ($dir['descripcion'] ?? ''));
        $colonia     = trim((string) ($dir['colonia'] ?? ''));
        $municipio   = trim((string) ($dir['municipio'] ?? ''));

        if ($descripcion !== '') {
            $componentes[] = $descripcion;
        }
        if ($colonia !== '') {
            $componentes[] = $colonia;
        }
        if ($municipio !== '') {
            $componentes[] = $municipio;
        }

        $direccion = implode(', ', $componentes);

        if (!empty($dir['referencia'])) {
            $direccion .= ' (Ref: ' . $dir['referencia'] . ')';
        }
        if (!empty($dir['telefono'])) {
            $direccion .= ' - Tel: ' . $dir['telefono'];
        }

        return $direccion !== '' ? $direccion : 'Dirección no especificada';
    }

    public function syncEnvioFromItems(): void
    {
        $envio = (float) $this->items()->sum('delivery_fee');
        $this->envio = $envio;
        $this->total = (float) $this->subtotal - (float) $this->descuento + $envio;
        $this->save();
    }

    public function refreshEstadoGlobalFromItems(): string
    {
        $statuses = $this->items()->pluck('fulfillment_status');

        if ($statuses->isEmpty()) {
            $this->estado_global = 'pendiente';
            $this->save();
            return $this->estado_global;
        }

        $estado = match (true) {
            $statuses->every(fn ($s) => $s === PedidoItem::ESTADO_ENTREGADO) => 'entregado',
            $statuses->every(fn ($s) => in_array($s, [PedidoItem::ESTADO_LISTO, PedidoItem::ESTADO_ENTREGADO], true)) => 'listo',
            $statuses->contains(PedidoItem::ESTADO_PREPARANDO) => 'preparando',
            default => 'pendiente',
        };

        $this->estado_global = $estado;
        $this->save();

        return $estado;
    }

    protected function direccionEnvio(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->normalizeJson($value),
        );
    }

    protected function facturacion(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->normalizeJson($value),
        );
    }

    protected function normalizeJson($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
