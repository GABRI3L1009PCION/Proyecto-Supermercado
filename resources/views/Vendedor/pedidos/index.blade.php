<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pedidos | Panel de vendedor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --vino:#722F37;
            --vino-oscuro:#501d24;
            --vino-claro:#f8eef0;
            --gris:#f5f5f5;
            --texto:#2C181A;
            --borde:#e6d6d9;
            --ok:#22c55e;
            --warning:#f97316;
            --info:#0ea5e9;
        }
        * { box-sizing: border-box; margin:0; padding:0; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; }
        body { background:var(--gris); color:var(--texto); }
        a { color:inherit; }
        .wrapper { max-width:1250px; margin:0 auto; padding:24px; }
        header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
        header h1 { font-size:1.8rem; color:var(--vino); display:flex; align-items:center; gap:10px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:10px; border:1px solid transparent; text-decoration:none; font-weight:600; transition:.2s; cursor:pointer; }
        .btn-primary { background:var(--vino); color:#fff; }
        .btn-primary:hover { background:var(--vino-oscuro); }
        .btn-secondary { background:#fff; border-color:var(--borde); }
        .btn-secondary:hover { background:var(--vino-claro); }
        .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:28px; }
        .stat-card { background:#fff; border-radius:14px; padding:18px; border:1px solid var(--borde); box-shadow:0 5px 18px rgba(0,0,0,.05); }
        .stat-title { font-size:.85rem; text-transform:uppercase; letter-spacing:.05em; color:#7c444c; }
        .stat-value { font-size:2rem; font-weight:800; color:var(--vino); margin-top:6px; }
        .filters { background:#fff; border-radius:14px; border:1px solid var(--borde); padding:18px; display:flex; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
        .filters form { display:flex; flex-wrap:wrap; gap:16px; width:100%; }
        .field { display:flex; flex-direction:column; gap:8px; }
        .field label { font-weight:600; font-size:.9rem; color:#6b313b; }
        .field input,
        .field select { padding:10px 12px; border-radius:10px; border:1px solid var(--borde); min-width:180px; background:#fff; }
        .field input:focus,
        .field select:focus { outline:none; border-color:var(--vino); box-shadow:0 0 0 3px rgba(114,47,55,.15); }
        .orders { display:grid; gap:18px; }
        .order-card { background:#fff; border-radius:16px; border:1px solid var(--borde); box-shadow:0 8px 20px rgba(0,0,0,.06); padding:20px; display:grid; gap:16px; }
        .order-header { display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; }
        .order-id { font-weight:700; font-size:1.1rem; color:var(--vino); display:flex; align-items:center; gap:10px; }
        .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:999px; font-size:.8rem; font-weight:700; }
        .badge-status { background:var(--vino-claro); color:var(--vino); }
        .badge-delivery { background:#dbeafe; color:#1d4ed8; }
        .order-body { display:grid; gap:12px; }
        .info-row { display:flex; gap:18px; flex-wrap:wrap; }
        .info-block { flex:1; min-width:220px; background:#faf8f8; border-radius:12px; border:1px solid var(--borde); padding:14px; }
        .info-block h3 { font-size:.95rem; color:#7c444c; font-weight:700; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
        .info-block p { margin-bottom:6px; line-height:1.4; font-size:.95rem; }
        .products { border-radius:12px; border:1px solid var(--borde); overflow:hidden; }
        .products table { width:100%; border-collapse:collapse; }
        .products th { background:var(--vino-claro); color:var(--vino); text-align:left; padding:10px 14px; font-size:.85rem; text-transform:uppercase; letter-spacing:.05em; }
        .products td { padding:12px 14px; border-top:1px solid var(--borde); font-size:.95rem; }
        .order-footer { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .total { font-size:1.1rem; font-weight:700; color:var(--vino); }
        .links { display:flex; gap:12px; flex-wrap:wrap; }
        .empty { text-align:center; padding:40px; border:2px dashed var(--borde); border-radius:16px; background:#fff; color:#7f6165; }
        .pagination { display:flex; justify-content:center; margin-top:24px; gap:6px; flex-wrap:wrap; }
        .pagination a,
        .pagination span { min-width:38px; text-align:center; padding:8px 12px; border-radius:10px; border:1px solid var(--borde); text-decoration:none; color:var(--texto); }
        .pagination .active { background:var(--vino); color:#fff; border-color:var(--vino); }
        @media (max-width:768px) {
            .wrapper { padding:16px; }
            .info-row { flex-direction:column; }
            .info-block { width:100%; }
        }
    </style>
</head>
<body>
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $pedidos */
    $estadoLabels = $estadoLabels ?? [];
    $deliveryLabels = $deliveryLabels ?? [];
@endphp
<div class="wrapper">
    <header>
        <h1><i class="fas fa-truck"></i> Mis pedidos</h1>
        <div class="actions">
            <a class="btn btn-secondary" href="{{ route('vendedor.dashboard') }}"><i class="fas fa-arrow-left"></i> Volver al panel</a>
            <a class="btn btn-primary" href="{{ route('vendedor.productos.index') }}"><i class="fas fa-box"></i> Mis productos</a>
        </div>
    </header>

    <section class="stats">
        <div class="stat-card">
            <div class="stat-title">Ítems totales</div>
            <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">En preparación</div>
            <div class="stat-value">{{ number_format($stats['pendientes'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Listos para entrega</div>
            <div class="stat-value">{{ number_format($stats['listos'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Entregados</div>
            <div class="stat-value">{{ number_format($stats['entregados'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Modo entrega: Yo entrego</div>
            <div class="stat-value" style="font-size:1.3rem;">{{ number_format($deliveryStats[\App\Models\PedidoItem::DELIVERY_VENDOR_SELF] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Modo entrega: Repartidor propio</div>
            <div class="stat-value" style="font-size:1.3rem;">{{ number_format($deliveryStats[\App\Models\PedidoItem::DELIVERY_VENDOR_COURIER] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Modo entrega: Supermercado</div>
            <div class="stat-value" style="font-size:1.3rem;">{{ number_format($deliveryStats[\App\Models\PedidoItem::DELIVERY_MARKET_COURIER] ?? 0) }}</div>
        </div>
    </section>

    <section class="filters">
        <form method="GET" action="{{ route('vendedor.pedidos.index') }}">
            <div class="field">
                <label for="buscar">Buscar pedido o cliente</label>
                <input type="text" id="buscar" name="q" value="{{ $busqueda }}" placeholder="Código, nombre, correo o teléfono">
            </div>
            <div class="field">
                <label for="estado">Estado del ítem</label>
                <select id="estado" name="estado">
                    <option value="">Todos</option>
                    @foreach ($estadoLabels as $key => $label)
                        <option value="{{ $key }}" @selected($estadoActual === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="delivery_mode">Modo de entrega</label>
                <select id="delivery_mode" name="delivery_mode">
                    <option value="">Todos</option>
                    @foreach ($deliveryLabels as $key => $label)
                        <option value="{{ $key }}" @selected($deliveryActual === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="align-self:flex-end;">
                <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Aplicar filtros</button>
            </div>
        </form>
    </section>

    <section class="orders">
        @forelse ($pedidos as $pedido)
            @php
                $items = $pedido->items ?? collect();
                $totalVendedor = $items->sum(fn ($item) => (float) $item->cantidad * (float) $item->precio_unitario + (float) $item->delivery_fee);
                $primerItem = $items->first();
                $status = $primerItem?->fulfillment_status;
                $deliveryMode = $primerItem?->delivery_mode;
                $direccion = $pedido->direccion_envio ?? [];
                $telefonoCliente = data_get($direccion, 'telefono') ?: optional($pedido->cliente)->telefono;
                $googleMaps = null;
                if (data_get($direccion, 'lat') && data_get($direccion, 'lng')) {
                    $googleMaps = sprintf('https://www.google.com/maps?q=%s,%s', data_get($direccion, 'lat'), data_get($direccion, 'lng'));
                }
            @endphp
            <article class="order-card">
                <div class="order-header">
                    <div class="order-id">
                        <i class="fas fa-receipt"></i>
                        Pedido {{ $pedido->codigo ?? ('PED-' . $pedido->id) }}
                    </div>
                    <div class="badges" style="display:flex; gap:8px; flex-wrap:wrap;">
                        @if ($status)
                            <span class="badge badge-status"><i class="fas fa-circle"></i> {{ $estadoLabels[$status] ?? ucfirst($status) }}</span>
                        @endif
                        @if ($deliveryMode)
                            <span class="badge badge-delivery"><i class="fas fa-truck"></i> {{ $deliveryLabels[$deliveryMode] ?? $deliveryMode }}</span>
                        @endif
                    </div>
                </div>
                <div class="order-body">
                    <div class="info-row">
                        <div class="info-block">
                            <h3><i class="fas fa-user"></i> Cliente</h3>
                            <p><strong>{{ optional($pedido->cliente)->name ?? 'Cliente no disponible' }}</strong></p>
                            @if ($telefonoCliente)
                                <p><i class="fas fa-phone"></i> {{ $telefonoCliente }}</p>
                            @endif
                            @if (optional($pedido->cliente)->email)
                                <p><i class="fas fa-envelope"></i> {{ optional($pedido->cliente)->email }}</p>
                            @endif
                        </div>
                        <div class="info-block">
                            <h3><i class="fas fa-map-marker-alt"></i> Entrega</h3>
                            <p>{{ $pedido->direccion_formateada }}</p>
                            @if ($googleMaps)
                                <p><a href="{{ $googleMaps }}" target="_blank" class="btn btn-secondary" style="padding:6px 10px; font-size:.85rem;"><i class="fas fa-location-arrow"></i> Ver en mapa</a></p>
                            @endif
                            <p><i class="fas fa-credit-card"></i> Pago: <strong>{{ ucfirst($pedido->metodo_pago ?? 'efectivo') }}</strong></p>
                        </div>
                        <div class="info-block">
                            <h3><i class="fas fa-file-invoice"></i> Facturación</h3>
                            @if (data_get($pedido->facturacion, 'requiere'))
                                <p><strong>NIT:</strong> {{ data_get($pedido->facturacion, 'nit', 'CF') }}</p>
                                <p>{{ data_get($pedido->facturacion, 'nombre') }}</p>
                                <p>{{ data_get($pedido->facturacion, 'direccion') }}</p>
                            @else
                                <p>El cliente no solicitó factura.</p>
                            @endif
                        </div>
                    </div>
                    <div class="products">
                        <table>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio unitario</th>
                                    <th>Entrega</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->producto->nombre ?? 'Producto no disponible' }}</strong>
                                            <br><small>Estado: {{ $estadoLabels[$item->fulfillment_status] ?? ucfirst($item->fulfillment_status) }}</small>
                                        </td>
                                        <td>{{ $item->cantidad }}</td>
                                        <td>Q{{ number_format($item->precio_unitario, 2) }}</td>
                                        <td>
                                            {{ $deliveryLabels[$item->delivery_mode] ?? $item->delivery_mode }}
                                            @if ($item->repartidor)
                                                <br><small><i class="fas fa-person-biking"></i> {{ $item->repartidor->name }}</small>
                                            @endif
                                            @if ($item->delivery_fee > 0)
                                                <br><small>Tarifa: Q{{ number_format($item->delivery_fee, 2) }}</small>
                                            @endif
                                        </td>
                                        <td>Q{{ number_format(($item->cantidad * $item->precio_unitario) + $item->delivery_fee, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="total">Total para ti: Q{{ number_format($totalVendedor, 2) }}</div>
                    <div class="links">
                        <a class="btn btn-secondary" href="{{ route('vendedor.pedidos.show', $pedido) }}"><i class="fas fa-eye"></i> Ver detalles</a>
                        <a class="btn btn-primary" href="{{ route('vendedor.pedidos.comprobante.pdf', $pedido) }}" target="_blank"><i class="fas fa-file-pdf"></i> Comprobante</a>
                        <a class="btn btn-secondary" href="{{ route('vendedor.pedidos.factura.pdf', $pedido) }}" target="_blank"><i class="fas fa-file-invoice-dollar"></i> Factura PDF</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty">
                <i class="fas fa-box-open fa-2x" style="margin-bottom:12px;"></i>
                <p>No se encontraron pedidos con los filtros actuales.</p>
            </div>
        @endforelse
    </section>

    @if ($pedidos->hasPages())
        <nav class="pagination">
            {{ $pedidos->onEachSide(1)->links() }}
        </nav>
    @endif
</div>
</body>
</html>
