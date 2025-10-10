<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseña extends Model
{
    use HasFactory;

    protected $table = 'reseñas';

    protected $fillable = [
        'producto_id',
        'cliente_id',
        'pedido_id',
        'pedido_item_id',
        'estrellas',
        'comentario',
        'respuesta_vendedor', // 💬 respuesta del vendedor
    ];

    protected $with = [
        'imagenes',
    ];

    /**
     * 🔹 Una reseña pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * 🔹 Una reseña pertenece a un cliente (usuario autenticado).
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * 🔹 Reseña asociada a un pedido (compra del supermercado).
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * 🔹 Reseña asociada a un ítem puntual del pedido.
     */
    public function pedidoItem()
    {
        return $this->belongsTo(PedidoItem::class, 'pedido_item_id');
    }

    /**
     * 🔹 Una reseña puede tener múltiples imágenes.
     */
    public function imagenes()
    {
        return $this->hasMany(ReseñaImagen::class, 'reseña_id');
    }

    /**
     * 🔹 Alias de compatibilidad para vistas antiguas que usaban ->fotos.
     */
    public function getFotosAttribute()
    {
        return $this->imagenes;
    }

    /**
     * 🔹 Accessor opcional: devuelve el promedio visual con estrellas (por si quieres mostrar ⭐ en vistas).
     */
    public function getEstrellasHtmlAttribute()
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $this->estrellas
                ? '<i class="fas fa-star" style="color:#f7b733"></i>'
                : '<i class="far fa-star" style="color:#f7b733"></i>';
        }
        return $html;
    }
}
