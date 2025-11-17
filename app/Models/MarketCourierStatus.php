<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketCourierStatus extends Model
{
    protected $fillable = ['status', 'note'];

    const STATUS_AVAILABLE = 'available';
    const STATUS_BUSY      = 'busy';
    const STATUS_OFFLINE   = 'offline';

    public static function options(): array
    {
        return [
            self::STATUS_AVAILABLE => [
                'label' => 'Disponible para reparto',
                'color' => '#16a34a',
            ],
            self::STATUS_BUSY => [
                'label' => 'Ocupado entregando pedido',
                'color' => '#f59e0b',
            ],
            self::STATUS_OFFLINE => [
                'label' => 'Fuera de servicio',
                'color' => '#dc2626',
            ],
        ];
    }

    public static function current(): self
    {
        $record = static::query()->latest('id')->first();

        if (!$record) {
            $record = static::create(['status' => self::STATUS_AVAILABLE]);
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
