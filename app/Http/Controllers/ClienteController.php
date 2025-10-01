<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:cliente'])->except(['categorias']);
    }

    public function categorias()
    {
        $categorias = Categoria::all();
        return view('cliente.categorias', compact('categorias'));
    }

    public function pedidoConfirmado()
    {
        // Obtener valores de la sesión
        $pedidoId = session('pedido_id');
        $pedidoCodigo = session('pedido_codigo');
        $pedidoRealizado = session('pedido_realizado', false);

        // Si no hay pedido en sesión, redirigir al catálogo
        if (!$pedidoId) {
            return redirect()->route('cliente.categorias')
                ->with('error', 'No se encontró información del pedido.');
        }

        return view('cliente.pedido_confirmado', [
            'pedidoId'        => $pedidoId,
            'pedidoCodigo'    => $pedidoCodigo,
            'pedidoRealizado' => $pedidoRealizado,
        ]);
    }

    public function estadoPedido(Pedido $pedido)
    {
        // ✅ CORREGIDO: ahora compara correctamente con user_id
        abort_unless($pedido->user_id === Auth::id(), 403, 'No autorizado');

        return view('cliente.estado_pedido', [
            'pedidoId'     => $pedido->id,
            'pedidoCodigo' => $pedido->codigo ?? ('PED-' . $pedido->id),
        ]);
    }

    public function estadoJson(Pedido $pedido)
    {
        // ✅ CORREGIDO: ahora compara correctamente con user_id
        abort_unless($pedido->user_id === Auth::id(), 403, 'No autorizado');

        $pedido->load('repartidor', 'items.producto');

        // Depuración - guarda en el log lo que se está enviando
        Log::debug('Estado JSON para pedido: ' . $pedido->id, [
            'estado_global' => $pedido->estado_global,
            'total' => $pedido->total,
            'direccion_envio' => $pedido->direccion_envio,
            'repartidor' => $pedido->repartidor,
            'items_count' => $pedido->items->count(),
            'user_id' => $pedido->user_id,
            'auth_id' => Auth::id()
        ]);

        // Mapeo de estados según tu tabla
        $estado = (string) $pedido->estado_global ?? 'pendiente';

        // Si necesitas más estados como 'asignado' y 'en_camino', deberías agregarlos a tu tabla
        // Por ahora trabajamos con los estados que tienes en tu BD
        if ($estado === 'preparando')  $estado = 'preparando';
        if ($estado === 'listo')       $estado = 'listo';
        if ($estado === 'entregado')   $estado = 'entregado';
        if ($estado === 'cancelado')   $estado = 'cancelado';

        // Obtener dirección desde el campo correcto (direccion_envio en lugar de direccion_entrega)
        $dir = null;
        if ($pedido->direccion_envio && is_array($pedido->direccion_envio)) {
            $dir = $pedido->direccion_envio;
        }

        // Obtener coordenadas desde la dirección de envío
        $lat = null;
        $lng = null;
        if ($dir && isset($dir['lat'])) {
            $lat = $dir['lat'];
        }
        if ($dir && isset($dir['lng'])) {
            $lng = $dir['lng'];
        }

        // Formatear dirección para mostrar
        $direccionTexto = '';
        if ($dir) {
            if (isset($dir['descripcion'])) $direccionTexto .= $dir['descripcion'];
            if (isset($dir['colonia'])) $direccionTexto .= ', ' . $dir['colonia'];
            if (isset($dir['referencia'])) $direccionTexto .= ' (Ref: ' . $dir['referencia'] . ')';
        }

        // Obtener productos
        $productos = [];
        if ($pedido->items) {
            foreach ($pedido->items as $item) {
                $productos[] = [
                    'nombre' => $item->producto->nombre ?? 'Producto no disponible',
                    'cantidad' => $item->cantidad,
                    'precio' => (float) $item->precio_unitario
                ];
            }
        }

        return response()->json([
            'id'        => $pedido->id,
            'codigo'    => $pedido->codigo ?? ('PED-' . $pedido->id),
            'estado'    => $estado,
            'total'     => (float) $pedido->total,
            'direccion' => $direccionTexto ?: 'Dirección no especificada',
            'direccion_completa' => $dir, // Enviar también el objeto completo por si acaso
            'lat'       => $lat,
            'lng'       => $lng,
            'productos' => $productos,
            'repartidor'=> $pedido->repartidor ? [
                'nombre'   => $pedido->repartidor->name ?? null,
                'telefono' => $pedido->repartidor->telefono ?? null,
                'foto'     => $pedido->repartidor->foto ?? null,
            ] : null,
            'updated_at'=> optional($pedido->updated_at)->toIso8601String(),
        ]);
    }
}
