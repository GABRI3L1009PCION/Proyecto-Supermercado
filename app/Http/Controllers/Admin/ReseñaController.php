<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseña;

class ReseñaController extends Controller
{
    public function index()
    {
        $reseñas = Reseña::with(['producto.vendor', 'cliente', 'pedido'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $promedioGeneral = round(Reseña::avg('estrellas') ?? 0, 2);
        $totalReseñas = Reseña::count();

        return view('admin.reseñas.index', compact('reseñas', 'promedioGeneral', 'totalReseñas'));
    }
}
