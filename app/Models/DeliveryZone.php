<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    /**
     * Campos que pueden asignarse de forma masiva.
     */
    protected $fillable = [
        'nombre',
        'municipio',
        'lat',
        'lng',
        'tarifa_base',
        'activo',
    ];

    /**
     * Casts automáticos para tipos de datos.
     */
    protected $casts = [
        'lat'         => 'float',
        'lng'         => 'float',
        'tarifa_base' => 'decimal:2',
        'activo'      => 'boolean',
    ];

    /**
     * Constantes para los municipios disponibles.
     */
    public const MUNICIPIO_PUERTO_BARRIOS = 'Puerto Barrios';
    public const MUNICIPIO_SANTO_TOMAS    = 'Santo Tomás de Castilla';

    /**
     * Retorna un arreglo con los municipios disponibles.
     */
    public static function municipiosDisponibles(): array
    {
        return [
            self::MUNICIPIO_PUERTO_BARRIOS,
            self::MUNICIPIO_SANTO_TOMAS,
        ];
    }

    /**
     * Scope para obtener únicamente las zonas activas.
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
