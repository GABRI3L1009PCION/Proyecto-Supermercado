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

        // 🔹 Obtiene reseñas reales con relaciones
        $reseñas = Reseña::whereHas('producto', function ($q) use ($vendedor) {
            $q->where('vendor_id', $vendedor->id);
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

        // 🔹 Envía datos a la vista
        return view('vendedor.reseñas.index', compact('reseñas', 'promedio', 'totalReseñas'));
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
        if ($reseña->producto->vendor_id !== Auth::id()) {
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
