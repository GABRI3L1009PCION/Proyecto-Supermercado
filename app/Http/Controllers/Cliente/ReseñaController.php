<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PedidoItem;
use App\Models\Reseña;
use App\Models\ReseñaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReseñaController extends Controller
{
    /**
     * Muestra los productos entregados y las reseñas del cliente.
     */
    public function index()
    {
        $cliente = Auth::user();

        $itemsEntregados = PedidoItem::with(['producto', 'pedido', 'reseña.imagenes'])
            ->whereHas('pedido', fn ($q) => $q->where('user_id', $cliente->id))
            ->where('fulfillment_status', PedidoItem::ESTADO_ENTREGADO)
            ->orderByDesc('updated_at')
            ->get();

        $reseñas = Reseña::with(['producto', 'pedido', 'imagenes'])
            ->where('cliente_id', $cliente->id)
            ->orderByDesc('created_at')
            ->get();

        return view('cliente.reseñas.index', [
            'itemsEntregados' => $itemsEntregados,
            'reseñas' => $reseñas,
        ]);
    }

    /**
     * Guarda una nueva reseña para un item entregado.
     */
    public function store(Request $request, PedidoItem $pedidoItem)
    {
        $cliente = Auth::user();

        abort_unless($pedidoItem->pedido && $pedidoItem->pedido->user_id === $cliente->id, 403);
        abort_unless($pedidoItem->fulfillment_status === PedidoItem::ESTADO_ENTREGADO, 422, 'El producto aún no ha sido entregado.');

        $data = $request->validate([
            'titulo' => ['nullable', 'string', 'max:150'],
            'estrellas' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:600'],
            'categoria_contexto' => ['nullable', 'string', 'max:40'],
            'aspectos' => ['nullable', 'array'],
            'aspectos.*' => ['string', 'max:60'],
            'tiempo_uso' => ['nullable', 'string', 'max:40'],
            'reaccion' => ['nullable', 'string', 'max:50'],
            'fotos' => ['nullable', 'array', 'max:6'],
            'fotos.*' => ['image', 'max:5120'],
        ]);

        if ($pedidoItem->reseña) {
            return redirect()->route('cliente.reseñas.index')
                ->with('warning', 'Este producto ya cuenta con una reseña.');
        }

        DB::transaction(function () use ($pedidoItem, $cliente, $data, $request) {
            $categoria = $data['categoria_contexto'] ?? null;
            if ($categoria && !array_key_exists($categoria, Reseña::CATEGORIAS_CONTEXTUALES)) {
                $categoria = null;
            }

            $tiempoUso = $data['tiempo_uso'] ?? null;
            if ($tiempoUso && !array_key_exists($tiempoUso, Reseña::TIEMPOS_USO)) {
                $tiempoUso = null;
            }

            $reaccion = $data['reaccion'] ?? null;
            if ($reaccion && !array_key_exists($reaccion, Reseña::REACCIONES)) {
                $reaccion = null;
            }

            $aspectosSeleccionados = collect($request->input('aspectos', []))
                ->filter(fn ($valor) => array_key_exists($valor, Reseña::ASPECTOS_CATALOGO))
                ->values()
                ->all();

            $reseña = Reseña::create([
                'producto_id' => $pedidoItem->producto_id,
                'cliente_id' => $cliente->id,
                'pedido_id' => $pedidoItem->pedido_id,
                'pedido_item_id' => $pedidoItem->id,
                'estrellas' => $data['estrellas'],
                'comentario' => $data['comentario'] ?? null,
                'titulo' => $data['titulo'] ?? null,
                'categoria_contexto' => $categoria,
                'aspectos' => $aspectosSeleccionados ?: null,
                'tiempo_uso' => $tiempoUso,
                'reaccion' => $reaccion,
            ]);

            $imagenes = collect($request->file('fotos', []))
                ->filter()
                ->take(6);

            foreach ($imagenes as $imagen) {
                $ruta = $imagen->store('reseñas', 'public');

                ReseñaImagen::create([
                    'reseña_id' => $reseña->id,
                    'ruta' => $ruta,
                ]);
            }
        });

        return redirect()->route('cliente.reseñas.index')
            ->with('status', '¡Gracias! Tu reseña fue registrada correctamente.');
    }
}
