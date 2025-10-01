<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteDetalle extends Model
{
    // Especificamos el nombre exacto de la tabla
    protected $table = 'clientes_detalle';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'user_id',
        'direccion',
        'telefono',
        'nit',
        'razon_social',
    ];
}
