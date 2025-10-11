<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reseña extends Model
{
    use HasFactory;

    public const TALLAS = ['pequena', 'exacta', 'grande'];

    public const REACCIONES = [
        'me_encanta' => '¡Me encanta!',
        'lo_volveria_a_comprar' => 'Lo volvería a comprar',
        'es_para_regalo' => 'Es para regalo',
        'necesita_mejoras' => 'Necesita mejoras',
    ];

    public const CATEGORIAS_CONTEXTUALES = [
        'alimentos' => 'Alimentos y bebidas',
        'hogar' => 'Hogar y limpieza',
        'tecnologia' => 'Tecnología y gadgets',
        'bienestar' => 'Cuidado personal y bienestar',
        'mascotas' => 'Productos para mascotas',
        'otros' => 'Otro tipo de producto',
    ];

    public const ASPECTOS_CATALOGO = [
        'presentacion_cuidada' => [
            'label' => 'Presentación cuidada',
            'icon' => 'fa-box',
            'tone' => 'positivo',
        ],
        'buen_sabor' => [
            'label' => 'Sabor/calidad sorprendentes',
            'icon' => 'fa-utensils',
            'tone' => 'positivo',
        ],
        'aroma_duradero' => [
            'label' => 'Aroma duradero',
            'icon' => 'fa-wind',
            'tone' => 'positivo',
        ],
        'entrega_rapida' => [
            'label' => 'Entrega puntual',
            'icon' => 'fa-truck-fast',
            'tone' => 'positivo',
        ],
        'no_funciono' => [
            'label' => 'No funcionó como esperaba',
            'icon' => 'fa-circle-exclamation',
            'tone' => 'alerta',
        ],
        'llego_danado' => [
            'label' => 'Llegó con detalles o daños',
            'icon' => 'fa-box-open',
            'tone' => 'alerta',
        ],
    ];

    public const TIEMPOS_USO = [
        'menos_semana' => 'Menos de una semana',
        'dos_semanas' => 'Alrededor de 2 semanas',
        'un_mes' => '1 mes',
        'tres_meses' => '3 meses',
        'seis_meses' => '6 meses',
        'mas_ano' => 'Más de un año',
    ];

    protected $table = 'reseñas';

    protected $fillable = [
        'producto_id',
        'cliente_id',
        'pedido_id',
        'pedido_item_id',
        'estrellas',
        'uso_score',
        'comodidad_score',
        'duracion_score',
        'talla_percibida',
        'reaccion',
        'comentario',
        'titulo',
        'categoria_contexto',
        'aspectos',
        'tiempo_uso',
        'respuesta_vendedor', // 💬 respuesta del vendedor
    ];

    protected $with = [
        'imagenes',
    ];

    protected $casts = [
        'aspectos' => 'array',
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

    public function getReaccionLabelAttribute(): ?string
    {
        if (!$this->reaccion) {
            return null;
        }

        return self::REACCIONES[$this->reaccion] ?? Str::title(str_replace('_', ' ', $this->reaccion));
    }

    public function getTallaPercibidaLabelAttribute(): ?string
    {
        return match ($this->talla_percibida) {
            'pequena' => 'Más pequeña',
            'exacta' => 'Talla exacta',
            'grande' => 'Más grande',
            default => null,
        };
    }

    public function getCategoriaContextoLabelAttribute(): ?string
    {
        if (!$this->categoria_contexto) {
            return null;
        }

        return self::CATEGORIAS_CONTEXTUALES[$this->categoria_contexto]
            ?? Str::title(str_replace('_', ' ', $this->categoria_contexto));
    }

    public function getTiempoUsoLabelAttribute(): ?string
    {
        if (!$this->tiempo_uso) {
            return null;
        }

        return self::TIEMPOS_USO[$this->tiempo_uso]
            ?? Str::title(str_replace('_', ' ', $this->tiempo_uso));
    }

    public function getAspectosDetalladosAttribute(): array
    {
        return collect($this->aspectos ?? [])
            ->map(function ($clave) {
                $config = self::ASPECTOS_CATALOGO[$clave] ?? null;

                if (!$config) {
                    return [
                        'clave' => $clave,
                        'label' => Str::title(str_replace('_', ' ', $clave)),
                        'icon' => 'fa-circle',
                        'tone' => 'neutral',
                    ];
                }

                return array_merge($config, [
                    'clave' => $clave,
                ]);
            })
            ->values()
            ->all();
    }

    public function getAspectosAgrupadosAttribute(): array
    {
        $detallados = collect($this->aspectos_detallados);

        return [
            'positivos' => $detallados->filter(fn ($item) => ($item['tone'] ?? null) === 'positivo')->values()->all(),
            'alertas' => $detallados->filter(fn ($item) => ($item['tone'] ?? null) === 'alerta')->values()->all(),
            'otros' => $detallados->filter(fn ($item) => !in_array($item['tone'] ?? null, ['positivo', 'alerta']))->values()->all(),
        ];
    }

    public function getResumenTitularAttribute(): string
    {
        if ($this->titulo) {
            return $this->titulo;
        }

        if ($this->comentario) {
            $comentario = Str::of($this->comentario)->trim();
            $oracion = (string) $comentario->before('.');
            $resumen = Str::of($oracion ?: $comentario)->trim();

            if ($resumen->isNotEmpty()) {
                return (string) $resumen->limit(80);
            }
        }

        if ($this->reaccion_label) {
            return $this->reaccion_label;
        }

        return match (true) {
            $this->estrellas >= 4 => 'Superó mis expectativas',
            $this->estrellas <= 2 => 'Necesita mejoras',
            default => 'Experiencia del cliente',
        };
    }
}
