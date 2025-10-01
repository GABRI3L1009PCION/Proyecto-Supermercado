<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Asegúrate de incluir TODOS los campos que usarás en create() o update()
    protected $fillable = ['nombre', 'slug', 'descripcion', 'estado'];
}
