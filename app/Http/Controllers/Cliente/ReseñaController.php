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
            'estrellas' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:600'],
            'fotos' => ['nullable', 'array', 'max:6'],
            'fotos.*' => ['image', 'max:5120'],
        ]);

        if ($pedidoItem->reseña) {
            return redirect()->route('cliente.reseñas.index')
                ->with('warning', 'Este producto ya cuenta con una reseña.');
        }

        DB::transaction(function () use ($pedidoItem, $cliente, $data, $request) {
            $reseña = Reseña::create([
                'producto_id' => $pedidoItem->producto_id,
                'cliente_id' => $cliente->id,
                'pedido_id' => $pedidoItem->pedido_id,
                'pedido_item_id' => $pedidoItem->id,
                'estrellas' => $data['estrellas'],
                'comentario' => $data['comentario'] ?? null,
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
