<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\MarketCourierStatus;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    /**
     * Listado de pedidos que contienen productos del vendedor autenticado.
     */
    public function index(Request $request)
    {
        $vendorId = $this->requireVendorId();

        $estado        = $request->query('estado');
        $deliveryMode  = $request->query('delivery_mode');
        $busqueda      = trim((string) $request->query('q', ''));
        $estadoValid   = array_keys($this->statusLabels());
        $deliveryValid = array_keys(PedidoItem::deliveryModeLabels());

        $pedidosQuery = Pedido::query()
            ->with([
                'cliente:id,name,telefono,email',
                'items' => function ($q) use ($vendorId, $estado, $deliveryMode, $estadoValid, $deliveryValid) {
                    $q->where('vendor_id', $vendorId)
                        ->when($estado && in_array($estado, $estadoValid, true), fn ($builder) => $builder->where('fulfillment_status', $estado))
                        ->when($deliveryMode && in_array($deliveryMode, $deliveryValid, true), fn ($builder) => $builder->where('delivery_mode', $deliveryMode))
                        ->with([
                            'producto:id,nombre,slug,vendor_id',
                            'repartidor:id,name,telefono',
                        ]);
                },
            ])
            ->whereHas('items', function ($q) use ($vendorId, $estado, $deliveryMode, $estadoValid, $deliveryValid) {
                $q->where('vendor_id', $vendorId)
                    ->when($estado && in_array($estado, $estadoValid, true), fn ($builder) => $builder->where('fulfillment_status', $estado))
                    ->when($deliveryMode && in_array($deliveryMode, $deliveryValid, true), fn ($builder) => $builder->where('delivery_mode', $deliveryMode));
            })
            ->latest();

        if ($busqueda !== '') {
            $pedidosQuery->where(function ($q) use ($busqueda) {
                $q->where('codigo', 'like', "%{$busqueda}%")
                    ->orWhereHas('cliente', function ($cliente) use ($busqueda) {
                        $cliente->where('name', 'like', "%{$busqueda}%")
                            ->orWhere('email', 'like', "%{$busqueda}%")
                            ->orWhere('telefono', 'like', "%{$busqueda}%");
                    });
            });
        }

        $pedidos = $pedidosQuery->paginate(10)->withQueryString();

        $statsBase = PedidoItem::query()->where('vendor_id', $vendorId);
        $stats = [
            'total'      => (clone $statsBase)->count(),
            'pendientes' => (clone $statsBase)->whereIn('fulfillment_status', [
                PedidoItem::ESTADO_ACEPTADO,
                PedidoItem::ESTADO_PREPARANDO,
            ])->count(),
            'listos'     => (clone $statsBase)->where('fulfillment_status', PedidoItem::ESTADO_LISTO)->count(),
            'entregados' => (clone $statsBase)->where('fulfillment_status', PedidoItem::ESTADO_ENTREGADO)->count(),
        ];

        $deliveryStats = [
            PedidoItem::DELIVERY_VENDOR_SELF    => (clone $statsBase)->where('delivery_mode', PedidoItem::DELIVERY_VENDOR_SELF)->count(),
            PedidoItem::DELIVERY_VENDOR_COURIER => (clone $statsBase)->where('delivery_mode', PedidoItem::DELIVERY_VENDOR_COURIER)->count(),
            PedidoItem::DELIVERY_MARKET_COURIER => (clone $statsBase)->where('delivery_mode', PedidoItem::DELIVERY_MARKET_COURIER)->count(),
        ];

        return view('Vendedor.pedidos.index', [
            'pedidos'        => $pedidos,
            'stats'          => $stats,
            'deliveryStats'  => $deliveryStats,
            'estadoActual'   => $estado,
            'deliveryActual' => $deliveryMode,
            'busqueda'       => $busqueda,
            'estadoLabels'   => $this->statusLabels(),
            'deliveryLabels' => PedidoItem::deliveryModeLabels(),
        ]);
    }

    /**
     * Mostrar un pedido al vendedor (solo sus ítems).
     */
    public function show(Pedido $pedido)
    {
        $vendorId = $this->requireVendorId();

        abort_unless(
            $pedido->items()->where('vendor_id', $vendorId)->exists(),
            403,
            'No puedes ver este pedido.'
        );

        $pedido->load([
            'cliente:id,name,email,telefono',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                    ->with([
                        'producto:id,nombre,slug,vendor_id',
                        'repartidor:id,name,telefono',
                    ]);
            },
        ]);

        // Normaliza posibles JSON guardados como string
        $dir = $pedido->direccion_envio ?? [];

        $telefonoCliente = data_get($dir, 'telefono') ?: data_get($pedido->cliente, 'telefono');

        $facturacion = [
            'requiere'  => (bool) data_get($pedido->facturacion, 'requiere', false),
            'nit'       => data_get($pedido->facturacion, 'nit', 'CF'),
            'nombre'    => data_get($pedido->facturacion, 'nombre'),
            'direccion' => data_get($pedido->facturacion, 'direccion'),
            'telefono'  => data_get($pedido->facturacion, 'telefono'),
        ];

        $lat = data_get($dir, 'lat');
        $lng = data_get($dir, 'lng');
        $dirTexto = $pedido->direccion_formateada;
        $googleMapsUrl = ($lat && $lng)
            ? sprintf('https://www.google.com/maps?q=%s,%s', $lat, $lng)
            : null;

        $itemsVendor = $pedido->items;
        $deliveryModes = $itemsVendor->pluck('delivery_mode')->filter()->unique();
        $deliveryFees  = $itemsVendor->pluck('delivery_fee')->filter()->unique();
        $deliveryInconsistent = $deliveryModes->count() > 1 || $deliveryFees->count() > 1;

        $primerItem = $itemsVendor->first();
        $delivery = [
            'mode'                  => $primerItem->delivery_mode ?? PedidoItem::DELIVERY_VENDOR_SELF,
            'fee'                   => (float) ($primerItem->delivery_fee ?? 0),
            'repartidor'            => $primerItem?->repartidor,
            'repartidor_id'         => $primerItem?->repartidor_id,
            'pickup_contact'        => $primerItem?->pickup_contact,
            'pickup_phone'          => $primerItem?->pickup_phone,
            'pickup_address'        => $primerItem?->pickup_address,
            'delivery_instructions' => $primerItem?->delivery_instructions,
            'vendor_zone_id'        => $primerItem?->vendor_zone_id,
        ];

        $repartidores = User::where('role', 'repartidor')
            ->where('estado', 'activo')
            ->orderBy('name')
            ->get(['id', 'name', 'telefono']);

        $vendorZones = optional(auth()->user()->vendor)
            ?->deliveryZones()
            ->where('activa', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'descripcion_cobertura', 'tarifa_reparto', 'activa']) ?? collect();

        $hasVendorZones = $vendorZones->isNotEmpty();

        $marketCourierStatus = MarketCourierStatus::current();

        return view('Vendedor.pedidos.show', [
            'pedido'              => $pedido,
            'pedidoItems'         => $itemsVendor, // ya filtrados por vendor
            'telefonoCliente'     => $telefonoCliente,
            'facturacion'         => $facturacion,
            'direccion'           => $dir,
            'direccionTexto'      => $dirTexto,
            'coordenadas'         => ['lat' => $lat, 'lng' => $lng, 'google' => $googleMapsUrl],
            'metodoPago'          => $pedido->metodo_pago,
            'delivery'            => $delivery,
            'deliveryLabels'      => PedidoItem::deliveryModeLabels(),
            'deliveryInconsistent'=> $deliveryInconsistent,
            'repartidores'        => $repartidores,
            'vendorZones'         => $vendorZones,
            'hasVendorZones'      => $hasVendorZones,
            'marketCourierStatus' => $marketCourierStatus->toArrayForDisplay(),
            'marketCourierStatusUpdatedAt' => optional($marketCourierStatus->updated_at)?->diffForHumans(),
            'marketCourierFee'    => config('market.courier_fee', 20),
            'marketCourierStatusEndpoint' => route('vendedor.repartidor.estado'),
            'estadoLabels'        => $this->statusLabels(),
        ]);
    }

    /**
     * PDF de FACTURA (estilo SAT simulado) con los ítems del vendor.
     * Ruta sugerida: vendedor.pedidos.factura.pdf
     */
    public function facturaPdf(Pedido $pedido)
    {
        $vendorId = $this->requireVendorId();
        abort_unless($pedido->items()->where('vendor_id', $vendorId)->exists(), 403);

        $pedido->load([
            'cliente:id,name,email,telefono',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                    ->with('producto:id,nombre,slug,vendor_id');
            },
        ]);

        $items    = $pedido->items;
        $subtotal = round($items->sum(fn($i) => (float) $i->precio_unitario * (int) $i->cantidad), 2);
        $iva      = round($subtotal * 0.12, 2);
        $total    = round($subtotal + $iva, 2);

        $empresa = [
            'nombre'    => 'Supermercado Atlantia',
            'direccion' => 'Av. Principal 123, Ciudad de Guatemala',
            'telefono'  => '+502 0000 0000',
            'email'     => 'ventas@atlantia.gt',
            'nit'       => '1234567-8',
            'logo'      => public_path('images/logo_atlantia.png'),
        ];

        $logoBase64 = file_exists($empresa['logo'])
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($empresa['logo']))
            : null;

        $serie       = 'A';
        $numero      = str_pad((string) $pedido->id, 8, '0', STR_PAD_LEFT);
        $nitReceptor = data_get($pedido->facturacion, 'nit', 'CF');

        $pdf = Pdf::loadView('vendedor.pdf.factura_sat', compact(
            'pedido', 'items', 'empresa', 'logoBase64', 'subtotal', 'iva', 'total', 'serie', 'numero', 'nitReceptor'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream("factura-pedido-{$pedido->id}.pdf");
    }

    /**
     * PDF de COMPROBANTE (recibo con logo y datos de la tienda).
     * Ruta sugerida: vendedor.pedidos.comprobante.pdf
     */
    public function comprobantePdf(Pedido $pedido)
    {
        $vendorId = $this->requireVendorId();
        abort_unless($pedido->items()->where('vendor_id', $vendorId)->exists(), 403);

        $pedido->load([
            'cliente:id,name,email,telefono',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                    ->with('producto:id,nombre,slug,vendor_id');
            },
        ]);

        $items    = $pedido->items;
        $subtotal = round($items->sum(fn($i) => (float) $i->precio_unitario * (int) $i->cantidad), 2);
        $iva      = round($subtotal * 0.12, 2);
        $total    = round($subtotal + $iva, 2);

        $empresa = [
            'nombre'    => 'Supermercado Atlantia',
            'direccion' => 'Av. Principal 123, Ciudad de Guatemala',
            'telefono'  => '+502 0000 0000',
            'email'     => 'ventas@atlantia.gt',
            'nit'       => '1234567-8',
            'logo'      => public_path('images/logo_atlantia.png'),
        ];

        $logoBase64 = file_exists($empresa['logo'])
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($empresa['logo']))
            : null;

        $pdf = Pdf::loadView('vendedor.pdf.comprobante', compact(
            'pedido', 'items', 'empresa', 'logoBase64', 'subtotal', 'iva', 'total'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream("comprobante-pedido-{$pedido->id}.pdf");
    }

    /**
     * Compatibilidad: aceptar un item desde rutas antiguas.
     */
    public function aceptarItem(PedidoItem $item): RedirectResponse
    {
        return $this->updateLegacyItemStatus($item, PedidoItem::ESTADO_ACEPTADO, 'Ítem aceptado correctamente.');
    }

    /**
     * Compatibilidad: rechazar un item desde rutas antiguas.
     */
    public function rechazarItem(PedidoItem $item): RedirectResponse
    {
        return $this->updateLegacyItemStatus($item, PedidoItem::ESTADO_RECHAZADO, 'Ítem rechazado correctamente.');
    }

    /**
     * Compatibilidad: actualizar el estado desde formularios anteriores.
     */
    public function actualizarEstado(Request $request, PedidoItem $item): RedirectResponse
    {
        $request->validate([
            'estado' => ['required', Rule::in(array_keys($this->statusLabels()))],
        ]);

        return $this->updateLegacyItemStatus($item, $request->estado, 'Estado actualizado correctamente.');
    }

    protected function updateLegacyItemStatus(PedidoItem $item, string $estado, string $mensaje): RedirectResponse
    {
        $vendorId = $this->ensureOwnsItem($item);

        if ($item->fulfillment_status === PedidoItem::ESTADO_ENTREGADO && $estado !== PedidoItem::ESTADO_ENTREGADO) {
            return back()->with('error', 'Este ítem ya fue entregado y no puede modificarse.');
        }

        $item->fulfillment_status = $estado;
        $item->save();

        $pedido = $item->pedido;
        if ($pedido) {
            $pedido->refreshEstadoGlobalFromItems();
        }

        return back()->with('ok', $mensaje)->with('vendor_id', $vendorId);
    }

    protected function ensureOwnsItem(PedidoItem $item): int
    {
        $vendorId = $this->requireVendorId();
        $ownerId  = $item->vendor_id ?? optional($item->producto)->vendor_id;

        abort_unless((int) $ownerId === (int) $vendorId, 403, 'No tienes permiso para gestionar este ítem.');

        return $vendorId;
    }

    protected function requireVendorId(): int
    {
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        return (int) $vendorId;
    }

    protected function statusLabels(): array
    {
        return [
            PedidoItem::ESTADO_ACEPTADO   => 'Aceptado',
            PedidoItem::ESTADO_PREPARANDO => 'Preparando',
            PedidoItem::ESTADO_LISTO      => 'Listo para entregar',
            PedidoItem::ESTADO_ENTREGADO  => 'Entregado',
            PedidoItem::ESTADO_RECHAZADO  => 'Rechazado',
            PedidoItem::ESTADO_CANCELADO  => 'Cancelado',
        ];
    }
}
