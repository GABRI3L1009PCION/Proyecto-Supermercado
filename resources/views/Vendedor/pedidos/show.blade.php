{{-- resources/views/Vendedor/pedidos/show.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido - Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <style>
        body{background:#f8f9fa;color:#2C181A;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        .container{max-width:1200px;margin:0 auto;padding:20px}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding-bottom:15px;border-bottom:2px solid #D9A6A6;gap:10px;flex-wrap:wrap}
        h1{color:#722F37;font-weight:700;margin:0}
        .btn-vino{background:#722F37;color:#fff;border:none;padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .btn-vino:hover{background:#8C3A44;transform:translateY(-1px);box-shadow:0 4px 8px rgba(114,47,55,.25)}
        .btn-outline-vino{background:#fff;border:1px solid #722F37;color:#722F37;padding:9px 12px;border-radius:8px;font-weight:600;display:inline-flex;align-items:center;gap:8px}
        .btn-outline-vino:hover{background:#F9F2F3}
        .card{background:#fff;border-radius:12px;padding:22px;margin-bottom:22px;box-shadow:0 4px 12px rgba(0,0,0,.08);border-left:4px solid #722F37}
        .card-header{border-bottom:1px solid #D9A6A6;margin-bottom:16px;padding-bottom:12px}
        .card-header h3{color:#722F37;font-weight:600;margin:0;display:flex;align-items:center;gap:10px}
        .info-list{list-style:none;padding:0;margin:0}
        .info-list li{margin-bottom:10px;display:flex;gap:10px;border-bottom:1px solid #eee;padding:6px 0}
        .info-list strong{min-width:160px;color:#8C3A44}
        .badge-estado{padding:6px 12px;border-radius:20px;font-weight:600;text-transform:capitalize}
        .badge-pendiente{background:#ffc107;color:#212529}
        .badge-aceptado{background:#17a2b8;color:#fff}
        .badge-preparando{background:#fd7e14;color:#fff}
        .badge-listo{background:#007bff;color:#fff}
        .badge-entregado{background:#28a745;color:#fff}
        .badge-rechazado{background:#dc3545;color:#fff}
        .table th{background:#F9F2F3;color:#722F37}
        .acciones-rapidas{display:flex;flex-wrap:wrap;gap:10px;margin-top:15px}
        .btn-accion{background:#F9F2F3;border:1px solid #D9A6A6;border-radius:8px;padding:10px 14px;font-weight:600;display:flex;align-items:center;gap:8px;cursor:pointer}
        .btn-accion:hover{background:#722F37;color:#fff}
        #mapa-envio{width:100%;height:260px;border-radius:12px;margin-top:15px}
        @media(max-width:768px){
            .header{flex-direction:column;align-items:flex-start}
            .acciones-rapidas{flex-direction:column}
        }
        @media print{.no-print{display:none!important}}
    </style>
</head>
<body>
@php
    // =================== Variables seguras ===================
    $pedido       = $pedido ?? null;
    $pedidoItems  = $pedidoItems ?? ($pedido->items ?? collect());
    $cliente      = $pedido->cliente ?? null;
    $codigo       = $pedido?->codigo ?? 'PED-'.$pedido->id ?? '—';
    $estadoGlobal = $pedido->estado_global ?? 'pendiente';
    $estadoBadge  = [
        'pendiente'=>'badge-pendiente','accepted'=>'badge-aceptado','aceptado'=>'badge-aceptado',
        'preparing'=>'badge-preparando','ready'=>'badge-listo','delivered'=>'badge-entregado',
        'rechazado'=>'badge-rechazado'
    ][$estadoGlobal] ?? 'badge-pendiente';

    $coords = $coordenadas ?? ['lat'=>null,'lng'=>null,'google'=>null];
    $dirTexto = $direccionTexto ?? null;
    $telefono = $telefonoCliente ?? ($cliente->telefono ?? null);
    $metodo   = $metodoPago ?? ($pedido->metodo_pago ?? null);

    // Nuevas inicializaciones seguras
    $delivery = $delivery ?? [
        'mode' => null,
        'fee'  => 0,
        'repartidor' => null,
        'repartidor_id' => null,
        'pickup_contact' => null,
        'pickup_phone' => null,
        'pickup_address' => null,
        'delivery_instructions' => null,
    ];
    $deliveryLabels = $deliveryLabels ?? [];
    $deliveryInconsistent = $deliveryInconsistent ?? false;
    $facturacion = $facturacion ?? ['requiere'=>false,'nit'=>'CF','nombre'=>null,'direccion'=>null,'telefono'=>null];
    $repartidores = $repartidores ?? collect();
    $estadoLabels = $estadoLabels ?? [];
    $estadoBadgeMap = [
        'pendiente' => 'badge-pendiente',
        'pending' => 'badge-pendiente',
        'accepted' => 'badge-aceptado',
        'aceptado' => 'badge-aceptado',
        'preparing' => 'badge-preparando',
        'preparando' => 'badge-preparando',
        'ready' => 'badge-listo',
        'listo' => 'badge-listo',
        'delivered' => 'badge-entregado',
        'entregado' => 'badge-entregado',
        'rejected' => 'badge-rechazado',
        'rechazado' => 'badge-rechazado',
        'canceled' => 'badge-rechazado',
        'cancelado' => 'badge-rechazado',
    ];
@endphp

<div class="container">
    <div class="header">
        <h1><i class="fas fa-shopping-bag me-2"></i> Pedido #{{ $pedido->id ?? '—' }}</h1>
        <div class="tools no-print">
            <a href="{{ route('vendedor.pedidos.index') }}" class="btn-outline-vino"><i class="fas fa-list"></i> Ver pedidos</a>
            <a href="{{ route('vendedor.dashboard') }}" class="btn-outline-vino"><i class="fas fa-arrow-left"></i> Volver</a>
            <a href="{{ route('vendedor.pedidos.comprobante.pdf', $pedido) }}" target="_blank" class="btn-outline-vino"><i class="fas fa-file-pdf"></i> Comprobante</a>
            <a href="{{ route('vendedor.pedidos.factura.pdf', $pedido) }}" target="_blank" class="btn-vino"><i class="fas fa-file-invoice"></i> Factura</a>
        </div>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa la información ingresada:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Información general --}}
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-info-circle"></i> Información del Pedido</h3></div>
        @if(!$pedido)
            <div class="alert alert-warning mb-0">No se encontró el pedido solicitado.</div>
        @else
            <ul class="info-list">
                <li><strong>Código:</strong> {{ $codigo }}</li>
                <li><strong>Cliente:</strong> {{ $cliente?->name ?? '—' }}</li>
                <li><strong>Fecha:</strong> {{ $pedido->created_at?->format('d/m/Y H:i') ?? '—' }}</li>
                <li><strong>Estado:</strong> <span class="badge-estado {{ $estadoBadge }}">{{ $estadoGlobal }}</span></li>
                <li><strong>Dirección:</strong> {{ $dirTexto ?? '—' }}</li>
                <li><strong>Pago:</strong> {{ $metodo ? str_replace('_',' ', $metodo) : '—' }}</li>
                <li><strong>Teléfono:</strong> {{ $telefono ?? '—' }}</li>
                <li><strong>Email:</strong> {{ $cliente?->email ?? '—' }}</li>
            </ul>
        @endif
    </div>

    {{-- Logística --}}
    <div class="repartidor-section no-print">
        <h4><i class="fas fa-truck"></i> Logística de entrega</h4>

        @if(!empty($deliveryInconsistent))
            <div class="alert alert-warning mt-2">Hay ítems con configuraciones distintas. Todos usarán la configuración seleccionada.</div>
        @endif

        <form action="{{ route('vendedor.pedidos.logistica', $pedido) }}" method="POST" class="mt-3">
            @csrf
            <div class="row g-3">
                <div class="col-lg-4 col-12">
                    <label class="form-label d-block">¿Quién entregará?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delivery_mode" id="deliverySelf"
                               value="{{ \App\Models\PedidoItem::DELIVERY_VENDOR_SELF }}"
                            {{ old('delivery_mode', $delivery['mode']) === \App\Models\PedidoItem::DELIVERY_VENDOR_SELF ? 'checked' : '' }}>
                        <label class="form-check-label" for="deliverySelf">Yo mismo</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delivery_mode" id="deliveryCourier"
                               value="{{ \App\Models\PedidoItem::DELIVERY_VENDOR_COURIER }}"
                            {{ old('delivery_mode', $delivery['mode']) === \App\Models\PedidoItem::DELIVERY_VENDOR_COURIER ? 'checked' : '' }}>
                        <label class="form-check-label" for="deliveryCourier">Repartidor aliado</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delivery_mode" id="deliveryMarket"
                               value="{{ \App\Models\PedidoItem::DELIVERY_MARKET_COURIER }}"
                            {{ old('delivery_mode', $delivery['mode']) === \App\Models\PedidoItem::DELIVERY_MARKET_COURIER ? 'checked' : '' }}>
                        <label class="form-check-label" for="deliveryMarket">Repartidor del supermercado</label>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <label class="form-label" for="repartidor_vendedor_select">Repartidor</label>
                    <select class="form-select" name="repartidor_id" id="repartidor_vendedor_select">
                        <option value="">Selecciona un repartidor</option>
                        @foreach($repartidores as $rep)
                            <option value="{{ $rep->id }}" @selected((int)old('repartidor_id', $delivery['repartidor_id']) === (int)$rep->id)>
                                {{ $rep->name }} {{ $rep->telefono ? '· '.$rep->telefono : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Disponible para repartidores aliados o del supermercado.</small>
                    @error('repartidor_id')<small class="text-danger d-block">{{ $message }}</small>@enderror
                </div>

                <div class="col-lg-4 col-12">
                    <label class="form-label" for="delivery_fee_input">Tarifa por entrega (Q)</label>
                    <input type="number" step="0.01" min="0" max="500" class="form-control" id="delivery_fee_input"
                           name="delivery_fee" value="{{ old('delivery_fee', number_format($delivery['fee'], 2, '.', '')) }}">
                </div>
            </div>

            <div class="row g-3 mt-2" id="marketFields" style="display:none;">
                <div class="col-md-4 col-12">
                    <label class="form-label" for="pickup_contact">Persona de contacto</label>
                    <input type="text" class="form-control" id="pickup_contact" name="pickup_contact"
                           value="{{ old('pickup_contact', $delivery['pickup_contact']) }}" maxlength="120">
                    @error('pickup_contact')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label" for="pickup_phone">Teléfono de contacto</label>
                    <input type="text" class="form-control" id="pickup_phone" name="pickup_phone"
                           value="{{ old('pickup_phone', $delivery['pickup_phone']) }}" maxlength="45">
                    @error('pickup_phone')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label" for="pickup_address">Dirección para recoger</label>
                    <input type="text" class="form-control" id="pickup_address" name="pickup_address"
                           value="{{ old('pickup_address', $delivery['pickup_address']) }}" maxlength="255">
                    @error('pickup_address')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="delivery_instructions">Indicaciones para la entrega</label>
                    <textarea class="form-control" id="delivery_instructions" name="delivery_instructions" rows="3" maxlength="500">{{ old('delivery_instructions', $delivery['delivery_instructions']) }}</textarea>
                    <small class="text-muted">Comparte referencias para que el repartidor pueda recoger y entregar el pedido sin contratiempos.</small>
                    @error('delivery_instructions')<small class="text-danger d-block">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="mt-3 d-flex flex-wrap gap-3 align-items-center">
                <button type="submit" class="btn-vino"><i class="fas fa-save"></i> Guardar logística</button>
                @if($delivery['mode'] === \App\Models\PedidoItem::DELIVERY_VENDOR_SELF)
                    <span class="badge bg-light text-dark">Entrega a cargo del vendedor</span>
                @elseif($delivery['mode'] === \App\Models\PedidoItem::DELIVERY_VENDOR_COURIER && $delivery['repartidor'])
                    <span class="badge bg-light text-dark">Asignado a: {{ $delivery['repartidor']->name }}</span>
                @elseif($delivery['mode'] === \App\Models\PedidoItem::DELIVERY_MARKET_COURIER && $delivery['repartidor'])
                    <span class="badge bg-light text-dark">Repartidor del supermercado: {{ $delivery['repartidor']->name }}</span>
                @endif
            </div>
        </form>
    </div>

    {{-- Gestión de ítems --}}
    <div class="card mt-4">
        <div class="card-header"><h3><i class="fas fa-cubes"></i> Ítems del pedido</h3></div>
        <div class="acciones-rapidas no-print">
            <form action="{{ route('vendedor.pedidos.estado', $pedido) }}" method="POST" class="d-flex flex-wrap gap-3 align-items-end">
                @csrf
                <div>
                    <label for="estado_global" class="form-label">Actualizar todos los ítems a</label>
                    <select name="estado" id="estado_global" class="form-select">
                        @foreach($estadoLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-vino"><i class="fas fa-sync"></i> Aplicar a todos</button>
            </form>
        </div>

        <div class="table-responsive mt-3">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Estado actual</th>
                        <th class="text-center">Actualizar estado</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pedidoItems as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->producto->nombre ?? 'Producto no disponible' }}</strong>
                            <br><small>Código: {{ $item->producto->codigo ?? 'N/D' }}</small>
                        </td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-center">
                            @php
                                $itemBadge = $estadoBadgeMap[$item->fulfillment_status] ?? 'badge-pendiente';
                            @endphp
                            <span class="badge-estado {{ $itemBadge }}">
                                {{ $estadoLabels[$item->fulfillment_status] ?? ucfirst($item->fulfillment_status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('vendedor.pedidoitems.estado', $item) }}" method="POST" class="d-inline-flex gap-2 align-items-center justify-content-center">
                                @csrf
                                <select name="estado" class="form-select form-select-sm" style="min-width:160px;">
                                    @foreach($estadoLabels as $key => $label)
                                        <option value="{{ $key }}" @selected($item->fulfillment_status === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-vino"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                        <td class="text-end">Q{{ number_format(($item->cantidad * $item->precio_unitario) + ($item->delivery_fee ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No hay ítems asociados al vendedor en este pedido.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const radios = document.querySelectorAll('input[name="delivery_mode"]');
        const select = document.getElementById('repartidor_vendedor_select');
        const marketFields = document.getElementById('marketFields');
        const toggle = () => {
            const checked = document.querySelector('input[name="delivery_mode"]:checked');
            const mode = checked ? checked.value : null;
            if (select) {
                select.disabled = !(mode === '{{ \App\Models\PedidoItem::DELIVERY_VENDOR_COURIER }}' || mode === '{{ \App\Models\PedidoItem::DELIVERY_MARKET_COURIER }}');
            }
            if (marketFields) {
                marketFields.style.display = mode === '{{ \App\Models\PedidoItem::DELIVERY_MARKET_COURIER }}' ? 'flex' : 'none';
            }
        };
        radios.forEach(r => r.addEventListener('change', toggle));
        toggle();
    });
</script>
</body>
</html>
