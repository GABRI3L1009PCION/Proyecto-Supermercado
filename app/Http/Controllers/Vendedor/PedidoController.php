<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoController extends Controller
{
    /**
     * Mostrar un pedido al vendedor (solo sus ítems).
     */
    public function show(Pedido $pedido)
    {
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        // Debe existir al menos 1 ítem de este vendor
        abort_unless(
            $pedido->items()->where('vendor_id', $vendorId)->exists(),
            403,
            'No puedes ver este pedido.'
        );

        // Carga relaciones y filtra ítems del vendor
        $pedido->load([
            'cliente:id,name,email,telefono',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                    ->with('producto:id,nombre,slug,vendor_id'); // sin "codigo"
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

        return view('Vendedor.pedidos.show', [
            'pedido'          => $pedido,
            'pedidoItems'     => $pedido->items, // ya filtrados por vendor
            'telefonoCliente' => $telefonoCliente,
            'facturacion'     => $facturacion,
            'direccion'       => $dir,
            'direccionTexto'  => $dirTexto,
            'coordenadas'     => ['lat' => $lat, 'lng' => $lng, 'google' => $googleMapsUrl],
            'metodoPago'      => $pedido->metodo_pago,
        ]);
    }

    /**
     * PDF de FACTURA (estilo SAT simulado) con los ítems del vendor.
     * Ruta sugerida: vendedor.pedidos.factura.pdf
     */
    public function facturaPdf(Pedido $pedido)
    {
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403);
        abort_unless($pedido->items()->where('vendor_id', $vendorId)->exists(), 403);

        $pedido->load([
            'cliente:id,name,email,telefono',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                    ->with('producto:id,nombre,slug,vendor_id');
            },
        ]);

        $items    = $pedido->items;
        $subtotal = round($items->sum(fn($i) => (float)$i->precio_unitario * (int)$i->cantidad), 2);
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
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($empresa['logo']))
            : null;

        // Simulación FEL
        $serie       = 'A';
        $numero      = str_pad((string)$pedido->id, 8, '0', STR_PAD_LEFT);
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
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403);
        abort_unless($pedido->items()->where('vendor_id', $vendorId)->exists(), 403);

        $pedido->load([
            'cliente:id,name,email,telefono',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                    ->with('producto:id,nombre,slug,vendor_id');
            },
        ]);

        $items    = $pedido->items;
        $subtotal = round($items->sum(fn($i) => (float)$i->precio_unitario * (int)$i->cantidad), 2);
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
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($empresa['logo']))
            : null;

        $pdf = Pdf::loadView('vendedor.pdf.comprobante', compact(
            'pedido', 'items', 'empresa', 'logoBase64', 'subtotal', 'iva', 'total'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream("comprobante-pedido-{$pedido->id}.pdf");
    }
}
