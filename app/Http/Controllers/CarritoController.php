<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\DeliveryZone;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CarritoController extends Controller
{
    public function catalogo(Request $request)
    {
        $query = Producto::with('categoria')
            ->withCount('reseñas')
            ->withAvg('reseñas', 'estrellas');

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

        $productosIds = array_keys($carrito);
        $productosDB = Producto::whereIn('id', $productosIds)->get()->keyBy('id');

        if (count($productosIds) !== $productosDB->count()) {
            $carritoFiltrado = [];
            foreach ($carrito as $productoId => $item) {
                if ($productosDB->has($productoId)) {
                    $carritoFiltrado[$productoId] = $item;
                }
            }

            session()->put('carrito', $carritoFiltrado);

            return redirect()
                ->route('carrito.ver')
                ->with('error', 'Actualizamos tu carrito porque algunos productos ya no están disponibles.');
        }

        foreach ($carrito as $productoId => &$itemCarrito) {
            $producto = $productosDB->get($productoId);
            if ($producto) {
                $itemCarrito['precio'] = (float) $producto->precio;
            }
        }
        unset($itemCarrito);

        session()->put('carrito', $carrito);

        $zones = DeliveryZone::activas()
            ->orderBy('municipio')
            ->orderBy('nombre')
            ->get();

        $zonas = $zones->map(function (DeliveryZone $zone) {
            return [
                'id'          => (int) $zone->id,
                'nombre'      => $zone->nombre,
                'municipio'   => $zone->municipio,
                'lat'         => $zone->lat,
                'lng'         => $zone->lng,
                'tarifa_base' => (float) ($zone->tarifa_base ?? 0),
            ];
        })->toArray();

        $municipios = $zones->pluck('municipio')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $mapaDefault = config('geografia.santo_tomas_default');
        $tarifaEnvioDefault = (float) (config('geografia.tarifa_envio_default') ?? 0);

        $subtotalCarrito = collect($carrito)->reduce(function ($carry, $item) {
            $precio   = (float) ($item['precio'] ?? 0);
            $cantidad = (int) ($item['cantidad'] ?? 0);
            return $carry + ($precio * $cantidad);
        }, 0.0);

        $tarifasEnvio = $zones->mapWithKeys(function (DeliveryZone $zone) use ($tarifaEnvioDefault) {
            $tarifa = $zone->tarifa_base;
            $valor = is_numeric($tarifa) ? (float) $tarifa : $tarifaEnvioDefault;
            return [$zone->id => $valor];
        })->toArray();

        return view('cliente.checkout', [
            'carrito'            => $carrito,
            'zonas'              => $zonas,
            'municipios'         => $municipios,
            'mapaDefault'        => $mapaDefault,
            'subtotalCarrito'    => $subtotalCarrito,
            'tarifasEnvio'       => $tarifasEnvio,
            'tarifaEnvioDefault' => $tarifaEnvioDefault,
        ]);
    }

    public function confirmarCheckout(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('carrito.ver')->with('error', 'El carrito está vacío.');
        }

        $zones = DeliveryZone::activas()->orderBy('municipio')->orderBy('nombre')->get();
        $zoneIds = $zones->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        $municipiosDisponibles = $zones->pluck('municipio')->unique()->values()->toArray();

        $data = $request->validate([
            'direccion'      => ['required', 'string', 'max:255'],
            'telefono'       => ['required', 'string', 'max:30'],
            'referencia'     => ['nullable', 'string', 'max:255'],
            'municipio'      => ['required', 'string', Rule::in($municipiosDisponibles)],
            'delivery_zone_id'=> ['required', 'integer', Rule::in($zoneIds)],
            'lat'            => ['nullable', 'numeric'],
            'lng'            => ['nullable', 'numeric'],
            'factura'        => ['required', 'in:si,no'],
            'nit'            => ['nullable', 'string', 'max:30', 'required_if:factura,si'],
            'razon_social'   => ['nullable', 'string', 'max:150', 'required_if:factura,si'],
            'nombre_empresa' => ['nullable', 'string', 'max:150'],
            'metodo_pago'    => ['nullable', 'string', 'max:50'],
            'costo_envio'    => ['nullable', 'numeric', 'min:0', 'max:500'],
        ], [
            'nit.required_if'          => 'El NIT es obligatorio si desea factura.',
            'razon_social.required_if' => 'La razón social es obligatoria si desea factura.',
        ]);

        $productosIds = array_keys($carrito);
        $productosDB = Producto::whereIn('id', $productosIds)->get()->keyBy('id');

        if (count($productosIds) !== $productosDB->count()) {
            $carritoFiltrado = [];
            foreach ($carrito as $productoId => $item) {
                if ($productosDB->has($productoId)) {
                    $carritoFiltrado[$productoId] = $item;
                }
            }

            session()->put('carrito', $carritoFiltrado);

            return redirect()
                ->route('carrito.ver')
                ->with('error', 'Actualizamos tu carrito porque algunos productos ya no están disponibles.');
        }

        $subtotal = 0;
        foreach ($carrito as $productoId => &$itemCarrito) {
            $producto = $productosDB->get($productoId);
            $precioUnitario = (float) ($producto->precio ?? $itemCarrito['precio'] ?? 0);
            $cantidad = (int) ($itemCarrito['cantidad'] ?? 0);

            $itemCarrito['precio'] = $precioUnitario;
            $subtotal += $precioUnitario * $cantidad;
        }
        unset($itemCarrito);

        session()->put('carrito', $carrito);

        $descuento = 0;
        $zone = $zones->firstWhere('id', (int) $data['delivery_zone_id']);
        if (!$zone) {
            return back()
                ->withErrors(['delivery_zone_id' => 'La zona seleccionada ya no está disponible.'])
                ->withInput();
        }

        $envioCalculado = $this->calcularCostoEnvio($zone);

        if (array_key_exists('costo_envio', $data) && $data['costo_envio'] !== null) {
            $envioReportado = (float) $data['costo_envio'];
            if (abs($envioReportado - $envioCalculado) <= 0.01) {
                $envioCalculado = $envioReportado;
            }
        }

        $envio = $envioCalculado;
        $total = $subtotal - $descuento + $envio;

        $direccionEnvio = [
            'descripcion' => $data['direccion'],
            'telefono'    => $data['telefono'],
            'referencia'  => $data['referencia'] ?? null,
            'colonia'     => $zone->nombre,
            'municipio'   => $zone->municipio,
            'zona_id'     => $zone->id,
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

        DB::transaction(function () use ($data, $subtotal, $descuento, $envio, $total, $direccionEnvio, $facturacion, $carrito, $productosDB, &$pedido) {
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
                'estado'          => 'pendiente',
                'direccion_envio' => $direccionEnvio,
                'facturacion'     => $facturacion,
            ]);

            $existeSupermercado = $productosDB->contains(fn ($producto) => $producto && $producto->vendor_id === null);
            $envioAsignado = false;

            foreach ($carrito as $productoId => $item) {
                $producto = $productosDB->get($productoId);
                if (!$producto) {
                    continue;
                }

                $esSupermercado = $producto->vendor_id === null;
                $debeAplicarEnvio = false;

                if (!$envioAsignado) {
                    if ($existeSupermercado) {
                        $debeAplicarEnvio = $esSupermercado;
                    } else {
                        $debeAplicarEnvio = true;
                    }
                }

                PedidoItem::create([
                    'pedido_id'         => $pedido->id,
                    'producto_id'       => (int) $productoId,
                    'vendor_id'         => $producto->vendor_id,
                    'cantidad'          => (int) $item['cantidad'],
                    'precio_unitario'   => (float) $item['precio'],
                    'fulfillment_status'=> PedidoItem::ESTADO_ACEPTADO,
                    'delivery_mode'     => $esSupermercado
                        ? PedidoItem::DELIVERY_MARKET_COURIER
                        : PedidoItem::DELIVERY_VENDOR_SELF,
                    'delivery_fee'      => $debeAplicarEnvio ? $envio : 0,
                    'repartidor_id'     => null,
                ]);

                if ($debeAplicarEnvio) {
                    $envioAsignado = true;
                }
            }

            $pedido->syncEnvioFromItems();
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

    protected function calcularCostoEnvio(?DeliveryZone $zone): float
    {
        $tarifaDefault = (float) (config('geografia.tarifa_envio_default') ?? 0);

        if (!$zone) {
            return $tarifaDefault;
        }

        $tarifa = $zone->tarifa_base;

        return is_numeric($tarifa) ? max(0, (float) $tarifa) : $tarifaDefault;
    }
}
