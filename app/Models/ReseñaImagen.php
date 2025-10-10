<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReseñaImagen extends Model
{
    use HasFactory;

    protected $table = 'reseña_imagenes';

    protected $fillable = [
        'reseña_id',
        'ruta',
    ];

    public function reseña()
    {
        return $this->belongsTo(Reseña::class, 'reseña_id');
    }
}
