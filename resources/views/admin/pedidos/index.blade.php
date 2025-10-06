<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión de Pedidos | Supermercado Atlantia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-o9N1j7kGStbFh8g1FVLzeuL1Yypw5v1sC0qf2m0wEgg=" crossorigin=""/>
    <style>
        :root{--vino:#800020;--vino-hover:#a1002c;--gris-claro:#f4f4f4;--borde:#e0e0e0;--texto:#333;--pendiente:#FF9800;--preparando:#9C27B0;--listo:#FFC107;--entregado:#4CAF50}
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        body{background:var(--gris-claro);padding:1rem;color:var(--texto)}
        .container{max-width:1400px;margin:0 auto;background:#fff;border-radius:10px;box-shadow:0 3px 10px #0000000d;overflow:hidden}
        header{background:var(--vino);color:#fff;padding:1.2rem 2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
        .header-left{display:flex;align-items:center;gap:1rem}
        .btn-back{background:#fff;color:var(--vino);text-decoration:none;padding:.6rem 1rem;border-radius:6px;font-weight:700;display:flex;align-items:center;gap:.5rem;transition:.3s}
        .btn-back:hover{background:#f0f0f0;transform:translateX(-3px)}
        h1{font-size:1.8rem;display:flex;align-items:center;gap:10px}
        .stats-container{display:flex;justify-content:space-around;flex-wrap:wrap;gap:1rem;padding:1.5rem;background:#f9f9f9;border-bottom:1px solid var(--borde)}
        .stat-card{background:#fff;padding:1rem 1.5rem;border-radius:8px;box-shadow:0 2px 5px #0000000d;text-align:center;min-width:180px;border-top:4px solid var(--vino)}
        .stat-number{font-size:2rem;font-weight:700;margin:.5rem 0;color:var(--vino)}
        .stat-title{font-size:.9rem;color:#777}
        .filtros{display:flex;gap:.8rem;padding:1.2rem 1.5rem;background:#fff;border-bottom:1px solid var(--borde);flex-wrap:wrap}
        .filtro-btn{padding:.6rem 1.2rem;border:1px solid var(--borde);background:#fff;border-radius:20px;cursor:pointer;transition:.3s;font-weight:500;display:flex;align-items:center;gap:.5rem}
        .filtro-btn.active,.filtro-btn:hover{background:var(--vino);color:#fff;border-color:var(--vino)}
        .estados{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem;padding:1.5rem}
        .estado-columna{background:#f9f9f9;border-radius:8px;overflow:hidden;border:1px solid var(--borde)}
        .estado-header{padding:1rem;font-weight:700;display:flex;justify-content:space-between;align-items:center;color:#fff}
        .pendiente-header{background:var(--pendiente)}.preparando-header{background:var(--preparando)}
        .listo-header{background:var(--listo)}.entregado-header{background:var(--entregado)}
        .contador{background:#ffffff4d;padding:.2rem .6rem;border-radius:12px;font-size:.9rem}
        .pedidos-list{padding:1rem;max-height:600px;overflow-y:auto}
        .pedido{background:#fff;border-radius:8px;padding:1rem;margin-bottom:1rem;box-shadow:0 2px 4px #0000000d;border-left:4px solid var(--pendiente)}
        .pedido-preparando{border-left-color:var(--preparando)}.pedido-listo{border-left-color:var(--listo)}.pedido-entregado{border-left-color:var(--entregado)}
        .pedido-id{font-weight:700;color:#555;margin-bottom:.5rem;display:flex;justify-content:space-between;align-items:center}
        .badge{background:#f0f0f0;padding:.2rem .5rem;border-radius:4px;font-size:.8rem}.badge-urgente{background:#ffcccc;color:#d32f2f}
        .pedido-cliente{font-weight:700;margin-bottom:.5rem;color:var(--texto)}
        .pedido-total{color:var(--vino);font-weight:700;margin-bottom:.5rem}
        .pedido-fecha{color:#777;font-size:.9rem;margin-bottom:.5rem}
        .pedido-info{color:#555;font-size:.9rem;margin-bottom:.8rem;display:flex;align-items:center;gap:.5rem}
        .pedido-productos{margin-top:.5rem;padding-top:.5rem;border-top:1px dashed #eee}
        .producto-item{display:flex;justify-content:space-between;margin-bottom:.3rem;font-size:.85rem}
        .pedido-acciones{display:flex;justify-content:flex-end;gap:.5rem;margin-top:.8rem}
        .btn{padding:.5rem 1rem;border:none;border-radius:4px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:.4rem;font-size:.9rem;transition:.2s}
        .btn-asignar{background:var(--vino);color:#fff}.btn-asignar:hover{background:var(--vino-hover)}
        .btn-detalles{background:#f0f0f0;color:var(--texto)}.btn-detalles:hover{background:#e0e0e0}
        .btn-preparar{background:var(--preparando);color:#fff}.btn-entregado{background:var(--entregado);color:#fff}
        .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:#00000080;z-index:1000;justify-content:center;align-items:center}
        .modal-content{background:#fff;border-radius:10px;width:90%;max-width:500px;max-height:90vh;overflow-y:auto;box-shadow:0 5px 15px #0000004d}
        .modal-header{background:var(--vino);color:#fff;padding:1rem;border-radius:10px 10px 0 0;display:flex;justify-content:space-between;align-items:center}
        .modal-header h2{margin:0;font-size:1.5rem}.close-modal{background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer}
        .modal-body{padding:1.5rem}.form-group{margin-bottom:1rem}
        .form-group label{display:block;margin-bottom:.5rem;font-weight:700}
        .form-group select,.form-group input{width:100%;padding:.75rem;border:1px solid var(--borde);border-radius:4px;font-size:1rem}
        .modal-footer{padding:1rem;border-top:1px solid var(--borde);display:flex;justify-content:flex-end;gap:1rem}
        .btn-cancel{background:#6c757d;color:#fff}.btn-confirm{background:var(--vino);color:#fff}
        .detalles-pedido{margin-bottom:1.5rem}.detalle-item{display:flex;justify-content:space-between;margin-bottom:.5rem;padding-bottom:.5rem;border-bottom:1px solid #eee}
        .detalle-label{font-weight:700;color:#555}.lista-productos{margin-top:1rem}.producto-detalle{display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #f0f0f0}
        #mapa-detalle{width:100%;height:220px;border-radius:10px;margin-top:1rem;border:1px solid #e0e0e0}
        @media (max-width:768px){.estados{grid-template-columns:1fr}.filtros{overflow-x:auto;padding-bottom:1rem}.stats-container{flex-direction:column;align-items:center}.stat-card{width:100%;max-width:300px}header{flex-direction:column;text-align:center}.header-left{flex-direction:column}.btn-back{align-self:flex-start}.modal-content{width:95%;margin:1rem}}
        @media (max-width:480px){body{padding:.5rem}header{padding:1rem}h1{font-size:1.5rem}.pedido-acciones{flex-direction:column}.btn{width:100%;justify-content:center}.modal-footer{flex-direction:column}}
    </style>
</head>
<body>

@php
    // Plantilla segura de URL para el form del modal (evita hardcodear)
    $asignarUrlTemplate = route('admin.pedidos.asignar-repartidor', ['pedido' => '__ID__']);

    $pedidosDataset = ($pedidos ?? collect())->map(function ($pedido) {
        $dir = $pedido->direccion_envio ?? [];
        $lat = data_get($dir, 'lat');
        $lng = data_get($dir, 'lng');
        $gmaps = ($lat && $lng) ? sprintf('https://www.google.com/maps?q=%s,%s', $lat, $lng) : null;
        $items = ($pedido->itemsSupermercado ?? collect())->map(function ($item) {
            $precio = (float) $item->precio_unitario;
            $cantidad = (int) $item->cantidad;
            return [
                'nombre'    => optional($item->producto)->nombre ?? 'Producto',
                'cantidad'  => $cantidad,
                'precio'    => $precio,
                'subtotal'  => round($precio * $cantidad, 2),
                'delivery_fee' => (float) ($item->delivery_fee ?? 0),
            ];
        })->values();

        return [
            'id'           => $pedido->id,
            'codigo'       => $pedido->codigo ?? ('PED-' . $pedido->id),
            'total'        => (float) $pedido->total,
            'estado'       => $pedido->estado_global,
            'metodo_pago'  => $pedido->metodo_pago,
            'created_at'   => optional($pedido->created_at)->format('d/m/Y H:i'),
            'cliente'      => [
                'nombre'  => optional($pedido->cliente)->name,
                'email'   => optional($pedido->cliente)->email,
                'telefono'=> optional($pedido->cliente)->telefono,
            ],
            'repartidor'   => [
                'nombre'  => optional($pedido->repartidor)->name,
                'telefono'=> optional($pedido->repartidor)->telefono,
            ],
            'direccion'    => [
                'texto'   => $pedido->direccion_formateada,
                'lat'     => $lat,
                'lng'     => $lng,
                'google'  => $gmaps,
                'referencia' => data_get($dir, 'referencia'),
            ],
            'facturacion'  => [
                'requiere'  => (bool) data_get($pedido->facturacion, 'requiere', false),
                'nit'       => data_get($pedido->facturacion, 'nit', 'CF'),
                'nombre'    => data_get($pedido->facturacion, 'nombre'),
                'direccion' => data_get($pedido->facturacion, 'direccion'),
            ],
            'items'        => $items,
        ];
    })->values();
@endphp

<div class="container">
    <header>
        <div class="header-left">
            <a href="{{ route('admin.panel') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Regresar</a>
            <h1><i class="fas fa-clipboard-list"></i> Gestión de Pedidos</h1>
        </div>
        <div class="header-info"><span>Actualizado: {{ now()->format('d/m/Y H:i') }}</span></div>
    </header>

    <div class="stats-container">
        <div class="stat-card"><div class="stat-title">Total Pedidos</div><div class="stat-number">{{ $totalPedidos ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-title">Pendientes</div><div class="stat-number">{{ $pendientesCount ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-title">Preparando</div><div class="stat-number">{{ $preparandoCount ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-title">Listos</div><div class="stat-number">{{ $listosCount ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-title">Entregados</div><div class="stat-number">{{ $entregadosCount ?? 0 }}</div></div>
    </div>

    <div class="filtros">
        <button class="filtro-btn active" data-estado="todos"><i class="fas fa-border-all"></i> Todos</button>
        <button class="filtro-btn" data-estado="pendiente"><i class="fas fa-clock"></i> Pendientes</button>
        <button class="filtro-btn" data-estado="preparando"><i class="fas fa-utensils"></i> Preparando</button>
        <button class="filtro-btn" data-estado="listo"><i class="fas fa-check-circle"></i> Listos</button>
        <button class="filtro-btn" data-estado="entregado"><i class="fas fa-truck"></i> Entregados</button>
    </div>

    <div class="estados">
        {{-- Pendientes --}}
        <div class="estado-columna">
            <div class="estado-header pendiente-header">
                <span><i class="fas fa-clock"></i> Pendientes</span>
                <span class="contador">{{ $pendientesCount ?? 0 }}</span>
            </div>
            <div class="pedidos-list">
                @forelse(($pedidos ?? collect())->where('estado_global','pendiente') as $pedido)
                    <div class="pedido">
                        <div class="pedido-id">
                            <span>ID: {{ $pedido->id }}</span>
                            @if(!empty($pedido->urgente))<span class="badge badge-urgente">Urgente</span>@endif
                        </div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>

                        @php($itemsSuper = $pedido->itemsSupermercado ?? collect())
                        @if($itemsSuper->count())
                            <div class="pedido-productos">
                                @foreach($itemsSuper as $item)
                                    <div class="producto-item">
                                        <span>{{ optional($item->producto)->nombre }} x{{ $item->cantidad }}</span>
                                        <span>Q{{ number_format(($item->precio_unitario ?? 0) * ($item->cantidad ?? 1), 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="pedido-info"><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada ?? 'Dirección no especificada' }}</div>
                        <div class="pedido-info"><i class="fas fa-money-check-alt"></i> {{ ucfirst(str_replace('_',' ', $pedido->metodo_pago ?? 'efectivo')) }}</div>

                        <div class="pedido-acciones">
                            <button type="button" class="btn btn-asignar" onclick="abrirModalAsignar({{ $pedido->id }})" {{ $itemsSuper->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-user-plus"></i> Asignar
                            </button>
                            <button class="btn btn-detalles" onclick="abrirModalDetalles({{ $pedido->id }})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            @if($itemsSuper->isEmpty())
                                <small class="text-muted">No hay productos del supermercado en este pedido.</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="pedido"><div class="pedido-info">No hay pedidos pendientes</div></div>
                @endforelse
            </div>
        </div>

        {{-- Preparando --}}
        <div class="estado-columna">
            <div class="estado-header preparando-header">
                <span><i class="fas fa-utensils"></i> Preparando</span>
                <span class="contador">{{ $preparandoCount ?? 0 }}</span>
            </div>
            <div class="pedidos-list">
                @forelse(($pedidos ?? collect())->where('estado_global','preparando') as $pedido)
                    <div class="pedido pedido-preparando">
                        <div class="pedido-id"><span>ID: {{ $pedido->id }}</span></div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        <div class="pedido-info"><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada ?? 'Dirección no especificada' }}</div>
                        <div class="pedido-info"><i class="fas fa-money-check-alt"></i> {{ ucfirst(str_replace('_',' ', $pedido->metodo_pago ?? 'efectivo')) }}</div>

                        <div class="pedido-acciones">
                            <form action="{{ route('admin.pedidos.actualizar-estado', $pedido->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="listo">
                                <button type="submit" class="btn btn-preparar"><i class="fas fa-check"></i> Marcar Listo</button>
                            </form>
                            <button class="btn btn-detalles" onclick="abrirModalDetalles({{ $pedido->id }})"><i class="fas fa-eye"></i> Ver</button>
                        </div>
                    </div>
                @empty
                    <div class="pedido"><div class="pedido-info">No hay pedidos en preparación</div></div>
                @endforelse
            </div>
        </div>

        {{-- Listos --}}
        <div class="estado-columna">
            <div class="estado-header listo-header">
                <span><i class="fas fa-check-circle"></i> Listos</span>
                <span class="contador">{{ $listosCount ?? 0 }}</span>
            </div>
            <div class="pedidos-list">
                @forelse(($pedidos ?? collect())->where('estado_global','listo') as $pedido)
                    <div class="pedido pedido-listo">
                        <div class="pedido-id"><span>ID: {{ $pedido->id }}</span></div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        <div class="pedido-info"><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada ?? 'Dirección no especificada' }}</div>
                        <div class="pedido-info"><i class="fas fa-money-check-alt"></i> {{ ucfirst(str_replace('_',' ', $pedido->metodo_pago ?? 'efectivo')) }}</div>

                        <div class="pedido-acciones">
                            <form action="{{ route('admin.pedidos.actualizar-estado', $pedido->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="entregado">
                                <button type="submit" class="btn btn-entregado"><i class="fas fa-truck"></i> Marcar Entregado</button>
                            </form>
                            <button class="btn btn-detalles" onclick="abrirModalDetalles({{ $pedido->id }})"><i class="fas fa-eye"></i> Ver</button>
                        </div>
                    </div>
                @empty
                    <div class="pedido"><div class="pedido-info">No hay pedidos listos</div></div>
                @endforelse
            </div>
        </div>

        {{-- Entregados --}}
        <div class="estado-columna">
            <div class="estado-header entregado-header">
                <span><i class="fas fa-truck"></i> Entregados</span>
                <span class="contador">{{ $entregadosCount ?? 0 }}</span>
            </div>
            <div class="pedidos-list">
                @forelse(($pedidos ?? collect())->where('estado_global','entregado') as $pedido)
                    <div class="pedido pedido-entregado">
                        <div class="pedido-id"><span>ID: {{ $pedido->id }}</span></div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        <div class="pedido-info"><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada ?? 'Dirección no especificada' }}</div>
                        <div class="pedido-info"><i class="fas fa-money-check-alt"></i> {{ ucfirst(str_replace('_',' ', $pedido->metodo_pago ?? 'efectivo')) }}</div>
                        <div class="pedido-info"><i class="fas fa-user"></i> Entregado por: {{ $pedido->repartidor->name ?? 'Sistema' }}</div>
                        <div class="pedido-acciones">
                            <button class="btn btn-detalles" onclick="abrirModalDetalles({{ $pedido->id }})"><i class="fas fa-eye"></i> Ver Detalles</button>
                        </div>
                    </div>
                @empty
                    <div class="pedido"><div class="pedido-info">No hay pedidos entregados</div></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modal Asignar --}}
<div id="modalAsignar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Asignar Repartidor</h2>
            <button class="close-modal" onclick="cerrarModal('modalAsignar')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formAsignar" action="" method="POST">
                @csrf
                {{-- Si quieres usar PUT, añade @method('PUT') y deja la ruta aceptar PUT --}}
                <input type="hidden" name="pedido_id" id="pedidoId">

                <div class="form-group">
                    <label for="repartidor_id">Seleccionar Repartidor:</label>
                    <select name="repartidor_id" id="repartidor_id" required>
                        <option value="">-- Seleccione un repartidor --</option>
                        @foreach($repartidores as $repartidor)
                            <option value="{{ $repartidor->id }}">{{ $repartidor->name }} - {{ $repartidor->email }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="delivery_fee_input">Tarifa de entrega (Q)</label>
                    <input type="number" name="delivery_fee" id="delivery_fee_input" step="0.01" min="0" max="500" value="0">
                    <small style="display:block;color:#6b7280;margin-top:6px;">Se sumará al total del cliente para los productos del supermercado.</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="cerrarModal('modalAsignar')">Cancelar</button>
                    <button type="submit" class="btn btn-confirm">Asignar Repartidor</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detalles --}}
<div id="modalDetalles" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detalles del Pedido</h2>
            <button class="close-modal" onclick="cerrarModal('modalDetalles')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="detallesContenido"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-cancel" onclick="cerrarModal('modalDetalles')">Cerrar</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o9N1j7kGStbFh8g1FVLzeuL1Yypw5v1sC0qf2m0wEgg=" crossorigin=""></script>
<script>
    // Plantilla de ruta generada por Laravel (más robusto que hardcodear)
    const ASIGNAR_URL_TEMPLATE = @json($asignarUrlTemplate);
    const PEDIDOS_DATA = @json($pedidosDataset);
    let detalleMap = null;

    function abrirModalAsignar(pedidoId) {
        document.getElementById('pedidoId').value = pedidoId;
        const form = document.getElementById('formAsignar');
        form.action = ASIGNAR_URL_TEMPLATE.replace('__ID__', pedidoId);
        const pedido = PEDIDOS_DATA.find(p => p.id === pedidoId);
        const feeInput = document.getElementById('delivery_fee_input');
        if (feeInput) {
            let fee = 0;
            if (pedido && pedido.items.length) {
                fee = pedido.items[0].delivery_fee || 0;
            }
            feeInput.value = (Math.round(fee * 100) / 100).toFixed(2);
        }
        document.getElementById('modalAsignar').style.display = 'flex';
    }

    function abrirModalDetalles(pedidoId) {
        const pedido = PEDIDOS_DATA.find(p => p.id === pedidoId);
        if (!pedido) {
            document.getElementById('detallesContenido').innerHTML = '<p>No se encontró la información del pedido.</p>';
            document.getElementById('modalDetalles').style.display = 'flex';
            return;
        }

        const productosHtml = pedido.items.length
            ? pedido.items.map(it => `
                <div class="producto-detalle">
                    <span>${it.nombre} x${it.cantidad}</span>
                    <span>Q${it.subtotal.toFixed(2)}</span>
                </div>
            `).join('')
            : '<div class="producto-detalle"><span>Sin productos del supermercado.</span><span></span></div>';

        const facturacionHtml = pedido.facturacion.requiere
            ? `<div class="detalle-item"><span class="detalle-label">Factura:</span><span>NIT ${pedido.facturacion.nit} · ${pedido.facturacion.nombre ?? '—'}</span></div>`
            : '<div class="detalle-item"><span class="detalle-label">Factura:</span><span>No requerida</span></div>';

        const direccionHtml = `
            <div class="detalle-item"><span class="detalle-label">Dirección:</span><span>${pedido.direccion.texto ?? '—'}</span></div>
            ${pedido.direccion.referencia ? `<div class="detalle-item"><span class="detalle-label">Referencia:</span><span>${pedido.direccion.referencia}</span></div>` : ''}
            ${pedido.direccion.google ? `<div class="detalle-item"><span class="detalle-label">Mapa:</span><span><a href="${pedido.direccion.google}" target="_blank">Abrir en Google Maps</a></span></div>` : ''}
        `;

        const repartidorHtml = pedido.repartidor.nombre
            ? `<div class="detalle-item"><span class="detalle-label">Repartidor:</span><span>${pedido.repartidor.nombre} (${pedido.repartidor.telefono ?? 'sin teléfono'})</span></div>`
            : '';

        const contenido = `
            <div class="detalles-pedido">
                <div class="detalle-item"><span class="detalle-label">Pedido:</span><span>#${pedido.codigo}</span></div>
                <div class="detalle-item"><span class="detalle-label">Cliente:</span><span>${pedido.cliente.nombre ?? '—'} (${pedido.cliente.telefono ?? 'sin teléfono'})</span></div>
                <div class="detalle-item"><span class="detalle-label">Correo:</span><span>${pedido.cliente.email ?? '—'}</span></div>
                <div class="detalle-item"><span class="detalle-label">Total:</span><span>Q${pedido.total.toFixed(2)}</span></div>
                <div class="detalle-item"><span class="detalle-label">Estado:</span><span>${pedido.estado}</span></div>
                <div class="detalle-item"><span class="detalle-label">Método de pago:</span><span>${(pedido.metodo_pago || 'efectivo').replace(/_/g,' ')}</span></div>
                <div class="detalle-item"><span class="detalle-label">Fecha:</span><span>${pedido.created_at ?? '—'}</span></div>
                ${facturacionHtml}
                ${direccionHtml}
                ${repartidorHtml}
            </div>
            <div class="lista-productos">
                <h3>Productos del supermercado</h3>
                ${productosHtml}
            </div>
            ${pedido.direccion.lat && pedido.direccion.lng ? '<div id="mapa-detalle"></div>' : ''}
        `;

        const contenedor = document.getElementById('detallesContenido');
        contenedor.innerHTML = contenido;
        document.getElementById('modalDetalles').style.display = 'flex';

        if (pedido.direccion.lat && pedido.direccion.lng) {
            setTimeout(() => {
                if (detalleMap) {
                    detalleMap.remove();
                }
                detalleMap = L.map('mapa-detalle').setView([pedido.direccion.lat, pedido.direccion.lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(detalleMap);
                L.marker([pedido.direccion.lat, pedido.direccion.lng]).addTo(detalleMap)
                    .bindPopup(pedido.direccion.texto || 'Ubicación de entrega');
            }, 50);
        }
    }

    function cerrarModal(id){
        document.getElementById(id).style.display='none';
        if (id === 'modalDetalles' && detalleMap) {
            detalleMap.remove();
            detalleMap = null;
        }
    }

    window.onclick = function(e){ if(e.target.classList.contains('modal')) cerrarModal(e.target.id); }

    document.addEventListener('DOMContentLoaded', () => {
        const filtros = document.querySelectorAll('.filtro-btn');
        filtros.forEach(btn => btn.addEventListener('click', function(){
            filtros.forEach(f=>f.classList.remove('active'));
            this.classList.add('active');
            const estado = this.dataset.estado;
            console.log('Filtrando por:', estado);
            // Aquí podrías ocultar/mostrar tarjetas según estado
        }));
    });
</script>

</body>
</html>
