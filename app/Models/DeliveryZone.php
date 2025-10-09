<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'municipio',
        'lat',
        'lng',
        'tarifa_base',
        'activo',
    ];

    protected $casts = [
        'lat'          => 'float',
        'lng'          => 'float',
        'tarifa_base'  => 'decimal:2',
        'activo'       => 'boolean',
    ];

    public const MUNICIPIO_PUERTO_BARRIOS = 'Puerto Barrios';
    public const MUNICIPIO_SANTO_TOMAS    = 'Santo Tomás de Castilla';

    public static function municipiosDisponibles(): array
    {
        return [
            self::MUNICIPIO_PUERTO_BARRIOS,
            self::MUNICIPIO_SANTO_TOMAS,
        ];
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
