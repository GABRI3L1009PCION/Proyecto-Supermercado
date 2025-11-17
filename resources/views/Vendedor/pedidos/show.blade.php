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
        .market-status{display:flex;align-items:center;gap:12px;background:#f3f4f6;border-radius:10px;padding:12px;margin-top:10px}
        .market-status__dot{width:14px;height:14px;border-radius:999px;display:inline-block}
        .zone-coverage-box{background:#fff7ed;border-radius:10px;padding:10px;font-size:.9rem;color:#92400e;margin-top:8px;display:none}
        .notice-card{background:#fff5f5;border:1px dashed #f87171;border-radius:8px;padding:12px;margin-top:10px;color:#7f1d1d}
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
        'vendor_zone_id' => null,
    ];
    $deliveryLabels = $deliveryLabels ?? [];
    $deliveryInconsistent = $deliveryInconsistent ?? false;
    $facturacion = $facturacion ?? ['requiere'=>false,'nit'=>'CF','nombre'=>null,'direccion'=>null,'telefono'=>null];
    $repartidores = $repartidores ?? collect();
    $vendorZones = $vendorZones ?? collect();
    $hasVendorZones = $vendorZones->count() > 0;
    $marketCourierStatus = $marketCourierStatus ?? [
        'status' => \App\Models\MarketCourierStatus::STATUS_AVAILABLE,
        'label'  => 'Disponible para reparto',
        'color'  => '#22c55e',
    ];
    $marketCourierStatusUpdatedAt = $marketCourierStatusUpdatedAt ?? null;
    $marketCourierFee = $marketCourierFee ?? 20;
    $marketCourierStatusEndpoint = $marketCourierStatusEndpoint ?? null;
    $estadoLabels = $estadoLabels ?? [];
    $selectedDeliveryMode = old('delivery_mode', $delivery['mode']);
    $selectedVendorZone = old('vendor_zone_id', $delivery['vendor_zone_id']);
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
                            {{ $selectedDeliveryMode === \App\Models\PedidoItem::DELIVERY_VENDOR_SELF ? 'checked' : '' }}
                            {{ $hasVendorZones ? '' : 'disabled' }}>
                        <label class="form-check-label" for="deliverySelf">Yo mismo</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delivery_mode" id="deliveryMarket"
                               value="{{ \App\Models\PedidoItem::DELIVERY_MARKET_COURIER }}"
                            {{ $selectedDeliveryMode === \App\Models\PedidoItem::DELIVERY_MARKET_COURIER ? 'checked' : '' }}>
                        <label class="form-check-label" for="deliveryMarket">Repartidor del supermercado</label>
                    </div>
                    @unless($hasVendorZones)
                        <div class="notice-card">
                            Necesitas crear al menos una zona para poder entregar tú mismo.
                            <a href="{{ route('vendedor.zonas.create') }}" class="fw-semibold">Crear zona de reparto</a>.
                        </div>
                    @endunless
                    <div class="notice-card" id="marketCourierNotice" style="display:none;">
                        <strong>Recuerda:</strong> el repartidor del supermercado cobra una tarifa fija de Q{{ number_format($marketCourierFee, 2) }}.
                    </div>
                    <div class="market-status" id="marketCourierStatusBox" data-endpoint="{{ $marketCourierStatusEndpoint }}">
                        <span class="market-status__dot" id="marketCourierStatusDot" style="background: {{ $marketCourierStatus['color'] ?? '#16a34a' }}"></span>
                        <div>
                            <div class="fw-semibold">Estado actual: <span id="marketCourierStatusLabel">{{ $marketCourierStatus['label'] ?? 'Disponible' }}</span></div>
                            <small class="text-muted" id="marketCourierStatusUpdatedAt">
                                {{ $marketCourierStatusUpdatedAt ? 'Actualizado ' . $marketCourierStatusUpdatedAt : '' }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-12" id="vendorZoneWrapper" {{ $selectedDeliveryMode === \App\Models\PedidoItem::DELIVERY_MARKET_COURIER ? 'style=display:none;' : '' }}>
                    <label class="form-label" for="vendor_zone_id">Zona de reparto</label>
                    <select class="form-select" name="vendor_zone_id" id="vendor_zone_id" {{ $hasVendorZones ? '' : 'disabled' }}>
                        <option value="">Selecciona una zona</option>
                        @foreach($vendorZones as $zone)
                            <option value="{{ $zone->id }}"
                                    data-fee="{{ number_format($zone->delivery_fee, 2, '.', '') }}"
                                    data-coverage="{{ $zone->coverage }}"
                                @selected((string)$selectedVendorZone === (string)$zone->id)>
                                {{ $zone->nombre }} (Q{{ number_format($zone->delivery_fee, 2) }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Administra tus zonas <a href="{{ route('vendedor.zonas.index') }}" target="_blank">aquí</a>.</small>
                    @error('vendor_zone_id')<small class="text-danger d-block">{{ $message }}</small>@enderror
                    <div class="zone-coverage-box" id="zoneCoverageBox"></div>
                </div>

                <div class="col-lg-4 col-12">
                    <label class="form-label" for="delivery_fee_input">Tarifa por entrega (Q)</label>
                    <input type="number" step="0.01" min="0" max="500" class="form-control" id="delivery_fee_input"
                           name="delivery_fee"
                           data-market-fee="{{ number_format($marketCourierFee, 2, '.', '') }}"
                           data-default-fee="{{ number_format($delivery['fee'], 2, '.', '') }}"
                           value="{{ old('delivery_fee', number_format($delivery['fee'], 2, '.', '')) }}">
                    <small class="text-muted">La tarifa se ajusta según la zona o servicio seleccionado.</small>
                </div>

                <div class="col-lg-4 col-12" id="repartidorWrapper" style="display:none;">
                    <label class="form-label" for="repartidor_vendedor_select">Repartidor del supermercado</label>
                    <select class="form-select" name="repartidor_id" id="repartidor_vendedor_select">
                        <option value="">Selecciona un repartidor</option>
                        @foreach($repartidores as $rep)
                            <option value="{{ $rep->id }}" @selected((int)old('repartidor_id', $delivery['repartidor_id']) === (int)$rep->id)>
                                {{ $rep->name }} {{ $rep->telefono ? '· '.$rep->telefono : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('repartidor_id')<small class="text-danger d-block">{{ $message }}</small>@enderror
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
        const marketFields = document.getElementById('marketFields');
        const zoneWrapper = document.getElementById('vendorZoneWrapper');
        const zoneSelect = document.getElementById('vendor_zone_id');
        const zoneCoverageBox = document.getElementById('zoneCoverageBox');
        const repartidorWrapper = document.getElementById('repartidorWrapper');
        const repartidorSelect = document.getElementById('repartidor_vendedor_select');
        const feeInput = document.getElementById('delivery_fee_input');
        const marketNotice = document.getElementById('marketCourierNotice');
        const marketFee = parseFloat(feeInput?.dataset.marketFee || '0');
        const defaultFee = parseFloat(feeInput?.dataset.defaultFee || feeInput?.value || '0');

        const toggleSections = () => {
            const checked = document.querySelector('input[name="delivery_mode"]:checked');
            const mode = checked ? checked.value : null;
            const isMarket = mode === '{{ \App\Models\PedidoItem::DELIVERY_MARKET_COURIER }}';
            if (zoneWrapper) {
                zoneWrapper.style.display = isMarket ? 'none' : '';
            }
            if (repartidorWrapper) {
                repartidorWrapper.style.display = isMarket ? '' : 'none';
            }
            if (repartidorSelect) {
                repartidorSelect.disabled = !isMarket;
            }
            if (marketFields) {
                marketFields.style.display = isMarket ? 'flex' : 'none';
            }
            if (marketNotice) {
                marketNotice.style.display = isMarket ? 'block' : 'none';
            }
            if (feeInput) {
                if (isMarket) {
                    feeInput.value = marketFee.toFixed(2);
                    feeInput.setAttribute('readonly', 'readonly');
                } else {
                    feeInput.removeAttribute('readonly');
                    const option = zoneSelect?.selectedOptions?.[0];
                    if (option && option.dataset.fee) {
                        feeInput.value = parseFloat(option.dataset.fee).toFixed(2);
                    } else {
                        feeInput.value = defaultFee.toFixed(2);
                    }
                }
            }
        };

        const updateZonePreview = () => {
            if (!zoneCoverageBox || !zoneSelect) return;
            const option = zoneSelect.selectedOptions[0];
            if (option && option.value) {
                const coverage = option.dataset.coverage || '';
                zoneCoverageBox.textContent = coverage ? 'Cobertura: ' + coverage : 'Sin cobertura definida para esta zona.';
                zoneCoverageBox.style.display = 'block';
                if (feeInput && option.dataset.fee) {
                    feeInput.value = parseFloat(option.dataset.fee).toFixed(2);
                }
            } else {
                zoneCoverageBox.style.display = 'none';
            }
        };

        radios.forEach(r => r.addEventListener('change', toggleSections));
        if (zoneSelect) {
            zoneSelect.addEventListener('change', () => {
                updateZonePreview();
                toggleSections();
            });
        }

        updateZonePreview();
        toggleSections();

        const statusBox = document.getElementById('marketCourierStatusBox');
        const statusEndpoint = statusBox?.dataset.endpoint;
        if (statusEndpoint) {
            const dot = document.getElementById('marketCourierStatusDot');
            const label = document.getElementById('marketCourierStatusLabel');
            const updatedAt = document.getElementById('marketCourierStatusUpdatedAt');
            const fetchStatus = () => {
                fetch(statusEndpoint)
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (!data) return;
                        if (dot && data.color) {
                            dot.style.background = data.color;
                        }
                        if (label && data.label) {
                            label.textContent = data.label;
                        }
                        if (updatedAt) {
                            updatedAt.textContent = 'Actualizado ' + new Date().toLocaleTimeString();
                        }
                    })
                    .catch(() => {});
            };

            fetchStatus();
            setInterval(fetchStatus, 30000);
        }
    });
</script>
</body>
</html>
