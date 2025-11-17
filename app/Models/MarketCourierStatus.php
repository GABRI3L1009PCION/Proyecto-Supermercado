<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketCourierStatus extends Model
{
    protected $fillable = ['status', 'note'];

    const STATUS_AVAILABLE = 'disponible_para_reparto';
    const STATUS_BUSY      = 'ocupado_entregando_pedido';
    const STATUS_OFFLINE   = 'fuera_de_servicio';

    public static function options(): array
    {
        return [
            self::STATUS_AVAILABLE => [
                'label' => 'Disponible para reparto',
                'color' => '#22c55e', // verde
            ],
            self::STATUS_BUSY => [
                'label' => 'Ocupado entregando pedido',
                'color' => '#facc15', // amarillo
            ],
            self::STATUS_OFFLINE => [
                'label' => 'Fuera de servicio',
                'color' => '#ef4444', // rojo
            ],
        ];
    }

    public static function current(): self
    {
        $record = static::query()->latest('id')->first();

        if (!$record) {
            $record = static::create([
                'status' => self::STATUS_AVAILABLE,
                'note'   => self::options()[self::STATUS_AVAILABLE]['label'],
            ]);
        }

        return $record;
    }

    public function toArrayForDisplay(): array
    {
        $meta = static::options()[$this->status] ?? [];

        return [
            'status' => $this->status,
            'label'  => $meta['label'] ?? ucfirst($this->status),
            'color'  => $meta['color'] ?? '#6b7280',
            'note'   => $this->note,
        ];
    }
}
