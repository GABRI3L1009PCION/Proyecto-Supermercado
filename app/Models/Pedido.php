<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'codigo', // Agregado para el código del pedido
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'envio' => 'decimal:2',
        'total' => 'decimal:2',
        'direccion_envio' => 'array',
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

        if (isset($dir['descripcion'])) $direccion .= $dir['descripcion'];
        if (isset($dir['colonia'])) $direccion .= ', ' . $dir['colonia'];
        if (isset($dir['referencia'])) $direccion .= ' (Ref: ' . $dir['referencia'] . ')';
        if (isset($dir['telefono'])) $direccion .= ' - Tel: ' . $dir['telefono'];

        return $direccion ?: 'Dirección no especificada';
    }
}
