<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    public function catalogo(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . trim($request->buscar) . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        $productos  = $query->get();
        $categorias = Categoria::all();

        $pedidoActivo = null;
        if (Auth::check()) {
            $pedidoActivo = Pedido::with('items')
                ->where('user_id', Auth::id())
                ->where(function ($q) {
                    $q->whereIn('estado_global', ['pendiente', 'preparando', 'listo'])
                        ->orWhere(function ($s) {
                            $s->whereHas('items', function ($i) {
                                $i->whereIn('fulfillment_status', ['accepted', 'preparing', 'ready']);
                            });
                        });
                })
                ->latest()
                ->first();
        }

        return view('cliente.catalogo', compact('productos', 'categorias', 'pedidoActivo'));
    }

    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'cantidad'    => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $carrito  = session()->get('carrito', []);

        if (isset($carrito[$producto->id])) {
            $carrito[$producto->id]['cantidad'] += (int)$request->cantidad;
        } else {
            $carrito[$producto->id] = [
                'nombre'   => $producto->nombre,
                'precio'   => (float)$producto->precio,
                'cantidad' => (int)$request->cantidad,
            ];
        }

        session()->put('carrito', $carrito);

        return redirect()->route('carrito.ver')->with('success', 'Producto agregado al carrito.');
    }

    public function ver()
    {
        $carrito = session()->get('carrito', []);
        return view('cliente.carrito', compact('carrito'));
    }

    public function checkout()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('carrito.ver')->with('error', 'El carrito está vacío.');
        }

        return view('cliente.checkout', compact('carrito'));
    }

    public function confirmarCheckout(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('carrito.ver')->with('error', 'El carrito está vacío.');
        }

        $data = $request->validate([
            'direccion'      => ['required', 'string', 'max:255'],
            'telefono'       => ['required', 'string', 'max:30'],
            'referencia'     => ['nullable', 'string', 'max:255'],
            'colonia'        => ['required', 'string', 'max:100'],
            'lat'            => ['nullable', 'numeric'],
            'lng'            => ['nullable', 'numeric'],
            'factura'        => ['required', 'in:si,no'],
            'nit'            => ['nullable', 'string', 'max:30', 'required_if:factura,si'],
            'razon_social'   => ['nullable', 'string', 'max:150', 'required_if:factura,si'],
            'nombre_empresa' => ['nullable', 'string', 'max:150'],
            'metodo_pago'    => ['nullable', 'string', 'max:50'],
        ], [
            'nit.required_if'          => 'El NIT es obligatorio si desea factura.',
            'razon_social.required_if' => 'La razón social es obligatoria si desea factura.',
        ]);

        $subtotal = 0;
        foreach ($carrito as $it) {
            $subtotal += ((float)$it['precio']) * ((int)$it['cantidad']);
        }

        $descuento = 0;
        $envio     = 0;
        $total     = $subtotal - $descuento + $envio;

        $direccionEnvio = [
            'descripcion' => $data['direccion'],
            'telefono'    => $data['telefono'],
            'referencia'  => $data['referencia'] ?? null,
            'colonia'     => $data['colonia'],
            'lat'         => $data['lat'] ?? null,
            'lng'         => $data['lng'] ?? null,
        ];

        $facturacion = [
            'requiere'  => $data['factura'] === 'si',
            'nit'       => $data['factura'] === 'si' ? ($data['nit'] ?? 'CF') : 'CF',
            'nombre'    => $data['factura'] === 'si'
                ? ($data['razon_social'] ?? $data['nombre_empresa'] ?? null)
                : null,
            'direccion' => $data['factura'] === 'si' ? $data['direccion'] : null,
            'telefono'  => $data['telefono'],
        ];

        $pedido = null;

        DB::transaction(function () use ($data, $subtotal, $descuento, $envio, $total, $direccionEnvio, $facturacion, $carrito, &$pedido) {
            $pedido = Pedido::create([
                'user_id'         => Auth::id(),
                'repartidor_id'   => null,
                'subtotal'        => $subtotal,
                'descuento'       => $descuento,
                'envio'           => $envio,
                'total'           => $total,
                'metodo_pago'     => $data['metodo_pago'] ?? 'efectivo',
                'estado_pago'     => 'pendiente',
                'estado_global'   => 'pendiente',
                'direccion_envio' => $direccionEnvio,
                'facturacion'     => $facturacion,
            ]);

            $productosDB = Producto::whereIn('id', array_keys($carrito))->get()->keyBy('id');

            foreach ($carrito as $productoId => $item) {
                $producto = $productosDB[$productoId];

                PedidoItem::create([
                    'pedido_id'         => $pedido->id,
                    'producto_id'       => (int)$productoId,
                    'vendor_id'         => $producto->vendor_id,
                    'cantidad'          => (int)$item['cantidad'],
                    'precio_unitario'   => (float)$item['precio'],
                    'fulfillment_status'=> 'accepted',
                    'repartidor_id'     => null,
                ]);
            }
        });

        session()->forget('carrito');

        // CORRECCIÓN: Usar put() en lugar de flash() para persistir los datos
        session()->put('pedido_realizado', true);
        session()->put('pedido_id', $pedido->id);
        session()->put('pedido_codigo', $pedido->codigo ?? ('PED-'.$pedido->id));
        session()->put('ultimo_pedido_id', $pedido->id);
        session()->put('ultimo_pedido_codigo', $pedido->codigo ?? ('PED-'.$pedido->id));

        return redirect()
            ->route('cliente.pedido.confirmado')
            ->with('success', 'Pedido confirmado correctamente.');
    }

    public function aumentar($id)
    {
        $carrito = session()->get('carrito', []);
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad'] = min(999, ((int)$carrito[$id]['cantidad']) + 1);
            session()->put('carrito', $carrito);
        }
        return back()->with('success', 'Cantidad aumentada.');
    }

    public function reducir($id)
    {
        $carrito = session()->get('carrito', []);
        if (isset($carrito[$id]) && $carrito[$id]['cantidad'] > 1) {
            $carrito[$id]['cantidad'] = max(1, ((int)$carrito[$id]['cantidad']) - 1);
            session()->put('carrito', $carrito);
        }
        return back()->with('success', 'Cantidad reducida.');
    }

    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }
        return back()->with('success', 'Producto eliminado.');
    }
}
