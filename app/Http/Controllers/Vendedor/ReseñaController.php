<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reseña;

class ReseñaController extends Controller
{
    /**
     * 📊 Muestra todas las reseñas de los productos
     * pertenecientes al vendedor autenticado.
     */
    public function index()
    {
        $vendedor = Auth::user();
        $vendorId = $vendedor?->vendorId();

        if (!$vendorId) {
            abort(403, 'No se encontró un perfil de vendedor asociado.');
        }

        // 🔹 Obtiene reseñas reales con relaciones
        $reseñas = Reseña::whereHas('producto', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
            ->with([
                'producto:id,nombre,vendor_id',
                'cliente:id,name',
                'imagenes', // relación con las fotos subidas por el cliente
                'pedido:id,codigo'
            ])
            ->orderByDesc('created_at')
            ->get();

        // 🔹 Cálculos de promedio y total
        $promedio = $reseñas->isNotEmpty() ? round($reseñas->avg('estrellas'), 1) : 0;
        $totalReseñas = $reseñas->count();

        $promediosCategoria = [
            'uso' => $reseñas->avg('uso_score'),
            'comodidad' => $reseñas->avg('comodidad_score'),
            'duracion' => $reseñas->avg('duracion_score'),
        ];

        $promediosCategoria = collect($promediosCategoria)
            ->map(fn ($valor) => $valor ? round($valor, 2) : null);

        $tallaDistribucion = collect(Reseña::TALLAS)
            ->mapWithKeys(fn ($talla) => [$talla => $reseñas->where('talla_percibida', $talla)->count()]);

        $reaccionesResumen = $reseñas
            ->filter(fn ($reseña) => filled($reseña->reaccion))
            ->groupBy('reaccion')
            ->map(fn ($grupo) => $grupo->count())
            ->sortDesc();

        // 🔹 Envía datos a la vista
        return view('vendedor.reseñas.index', [
            'reseñas' => $reseñas,
            'promedio' => $promedio,
            'totalReseñas' => $totalReseñas,
            'promediosCategoria' => $promediosCategoria,
            'tallaDistribucion' => $tallaDistribucion,
            'reaccionesResumen' => $reaccionesResumen,
        ]);
    }

    /**
     * 💬 Permite al vendedor responder públicamente a una reseña.
     */
    public function responder(Request $request, $reseñaId)
    {
        // 🧩 Validar datos de entrada
        $request->validate([
            'respuesta_vendedor' => 'required|string|max:500',
        ]);

        // 🔍 Buscar la reseña con el producto asociado
        $reseña = Reseña::with('producto')->findOrFail($reseñaId);

        // 🔒 Seguridad: solo el vendedor propietario puede responder
        $vendorId = Auth::user()?->vendorId();

        if (!$vendorId || $reseña->producto->vendor_id !== $vendorId) {
            abort(403, 'No autorizado para responder esta reseña.');
        }

        // ✏️ Actualiza la respuesta
        $reseña->update([
            'respuesta_vendedor' => $request->respuesta_vendedor,
        ]);

        // 🔔 Redirigir con mensaje de éxito
        return back()->with('success', 'Respuesta enviada correctamente.');
    }
}
