{{-- resources/views/Vendedor/pedidos/show.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido - Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Paleta fija (sin variables CSS) */
        /* vino primario: #722F37 | secundario: #8C3A44 | claro: #F9F2F3 | acento: #D9A6A6 | texto: #2C181A */
        body{background:#f8f9fa;color:#2C181A;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        .container{max-width:1200px;margin:0 auto;padding:20px}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding-bottom:15px;border-bottom:2px solid #D9A6A6;gap:10px;flex-wrap:wrap}
        h1{color:#722F37;font-weight:700;margin:0}
        .btn-vino{background:#722F37;color:#fff;border:none;padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:600;transition:.25s;display:inline-flex;align-items:center;gap:8px}
        .btn-vino:hover{background:#8C3A44;transform:translateY(-1px);box-shadow:0 4px 8px rgba(114,47,55,.25);color:#fff}
        .btn-outline-vino{background:#fff;border:1px solid #722F37;color:#722F37;padding:9px 12px;border-radius:8px;font-weight:600;display:inline-flex;align-items:center;gap:8px}
        .btn-outline-vino:hover{background:#F9F2F3}
        .card{background:#fff;border-radius:12px;padding:22px;margin-bottom:22px;box-shadow:0 4px 12px rgba(0,0,0,.08);border-left:4px solid #722F37}
        .card-header{border-bottom:1px solid #D9A6A6;margin-bottom:16px;padding-bottom:12px}
        .card-header h3{color:#722F37;font-weight:600;margin:0;display:flex;align-items:center;gap:10px}
        .info-list{list-style:none;padding-left:0;margin-bottom:0}
        .info-list li{margin-bottom:12px;padding:8px 0;display:flex;border-bottom:1px solid #f0f0f0;gap:12px}
        .info-list li:last-child{margin-bottom:0;border-bottom:none}
        .info-list strong{min-width:170px;color:#8C3A44;display:flex;align-items:center;gap:8px}
        .badge-estado{display:inline-block;padding:6px 12px;border-radius:20px;font-weight:700;font-size:.85rem;text-transform:capitalize}
        .badge-pendiente{background:#ffc107;color:#212529}
        .badge-aceptado{background:#17a2b8;color:#fff}
        .badge-preparando{background:#fd7e14;color:#fff}
        .badge-listo{background:#007bff;color:#fff}
        .badge-entregado{background:#28a745;color:#fff}
        .badge-rechazado{background:#dc3545;color:#fff}
        .table{width:100%;border-collapse:collapse;margin-top:10px;border-radius:8px;overflow:hidden}
        .table thead th{background:#F9F2F3;color:#722F37;padding:12px 14px;text-align:left;font-weight:700;border-bottom:2px solid #D9A6A6}
        .table td{padding:12px 14px;border-bottom:1px solid #eee;vertical-align:middle}
        .producto-info{display:flex;flex-direction:column}
        .producto-nombre{font-weight:700;color:#722F37}
        .producto-meta{font-size:.85rem;color:#6c757d}
        .estado-form{display:flex;gap:10px;align-items:center}
        .form-select{border-radius:8px;padding:8px 12px;border:1px solid #ced4da;min-width:160px}
        .acciones-rapidas{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
        .btn-accion{display:flex;align-items:center;gap:8px;padding:10px 14px;background:#F9F2F3;border:1px solid #D9A6A6;border-radius:8px;cursor:pointer;font-weight:600}
        .btn-accion:hover{background:#722F37;color:#fff}
        .repartidor-section{background:#F9F2F3;padding:16px;border-radius:10px;margin-top:16px}
        .repartidor-select{display:flex;gap:10px;align-items:center;margin-top:10px;flex-wrap:wrap}
        .tools{display:flex;gap:8px;flex-wrap:wrap}
        @media (max-width:768px){
            .header{flex-direction:column;align-items:flex-start;gap:12px}
            .info-list li{flex-direction:column}
            .info-list strong{min-width:auto}
            .estado-form{flex-direction:column;align-items:flex-start}
            .acciones-rapidas{flex-direction:column}
            .repartidor-select{flex-direction:column;align-items:flex-start}
        }
        @media print{
            .no-print{display:none!important}
            body{background:#fff}
            .card{box-shadow:none;border-left:none}
        }
    </style>
</head>
<body>
@php
    /** @var \App\Models\Pedido|null $pedido */
    $pedido       = $pedido ?? null;
    $pedidoItems  = $pedidoItems ?? ($pedido->items ?? collect());
    $cliente      = $pedido->cliente ?? null;

    // Código y estado global
    $codigo       = $pedido?->codigo ?? (isset($pedido->id) ? 'PED-'.$pedido->id : 'PED-—');
    $estadoGlobal = $pedido->estado_global ?? 'pendiente';
    $estadoBadge  = [
        'pendiente'=>'badge-pendiente','accepted'=>'badge-aceptado','aceptado'=>'badge-aceptado',
        'preparing'=>'badge-preparando','preparando'=>'badge-preparando','ready'=>'badge-listo',
        'listo'=>'badge-listo','delivered'=>'badge-entregado','entregado'=>'badge-entregado',
        'rejected'=>'badge-rechazado','rechazado'=>'badge-rechazado',
    ][$estadoGlobal] ?? 'badge-pendiente';

    // Dirección (puede venir como string/array/objeto)
    $dirRaw = $pedido->direccion_envio ?? null;
    $dirArr = null;
    if (is_string($dirRaw))      $dirArr = json_decode($dirRaw, true) ?: null;
    elseif (is_object($dirRaw))  $dirArr = (array) $dirRaw;
    elseif (is_array($dirRaw))   $dirArr = $dirRaw;

    $dirTexto = $dirArr
        ? trim(($dirArr['direccion'] ?? '') . (isset($dirArr['referencia']) ? ' (Ref: '.$dirArr['referencia'].')' : ''))
        : (is_string($dirRaw) ? $dirRaw : null);

    // Teléfono con fallbacks
    $telefono = data_get($dirArr, 'telefono') ?? ($cliente->telefono ?? null);

    // Facturación (puede ser JSON string / array / obj)
    $facRaw = $pedido->facturacion ?? null;
    if (is_string($facRaw)) $facRaw = json_decode($facRaw, true) ?: [];
    if (is_object($facRaw)) $facRaw = (array) $facRaw;
    $facturacion = [
        'requiere'  => (bool) data_get($facRaw, 'requiere', false),
        'nit'       => data_get($facRaw, 'nit', 'CF'),
        'nombre'    => data_get($facRaw, 'nombre'),
        'direccion' => data_get($facRaw, 'direccion'),
    ];
@endphp

<div class="container">
    <div class="header">
        <h1 class="mb-0">
            <i class="fas fa-shopping-bag me-2"></i>
            Pedido #{{ $pedido->id ?? '—' }}
        </h1>

        <div class="tools no-print">
            <a href="{{ route('vendedor.dashboard') }}" class="btn-outline-vino">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </a>

            {{-- BOTONES PDF --}}
            <a href="{{ route('vendedor.pedidos.factura.pdf', $pedido) }}" target="_blank" class="btn-vino">
                <i class="fas fa-file-invoice"></i> Imprimir factura
            </a>
            <a href="{{ route('vendedor.pedidos.comprobante.pdf', $pedido) }}" target="_blank" class="btn-outline-vino">
                <i class="fas fa-receipt"></i> Imprimir comprobante
            </a>
        </div>
    </div>

    {{-- Información del Pedido --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Información del Pedido</h3>
        </div>

        @if(!$pedido)
            <div class="alert alert-warning mb-0">No se encontró el pedido solicitado.</div>
        @else
            <ul class="info-list">
                <li>
                    <strong><i class="fas fa-hashtag"></i> Código:</strong>
                    <span>{{ $codigo }}</span>
                </li>
                <li>
                    <strong><i class="fas fa-user"></i> Cliente:</strong>
                    <span>{{ $cliente?->name ?? '—' }}</span>
                </li>
                <li>
                    <strong><i class="fas fa-calendar"></i> Fecha:</strong>
                    <span>{{ $pedido->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                </li>
                <li>
                    <strong><i class="fas fa-tag"></i> Estado:</strong>
                    <span class="badge-estado {{ $estadoBadge }}">{{ $estadoGlobal }}</span>
                </li>
                <li>
                    <strong><i class="fas fa-map-marker-alt"></i> Dirección:</strong>
                    <span>{{ $dirTexto ?? '—' }}</span>
                </li>
                <li>
                    <strong><i class="fas fa-phone"></i> Teléfono:</strong>
                    <span>{{ $telefono ?? '—' }}</span>
                </li>
                <li>
                    <strong><i class="fas fa-envelope"></i> Email:</strong>
                    <span>{{ $cliente?->email ?? '—' }}</span>
                </li>
            </ul>

            @if($facturacion['requiere'])
                <div class="repartidor-section mt-3">
                    <h4><i class="fas fa-file-invoice"></i> Datos de facturación</h4>
                    <ul class="info-list">
                        <li><strong><i class="fas fa-id-card"></i> NIT:</strong> <span>{{ $facturacion['nit'] ?? 'CF' }}</span></li>
                        <li><strong><i class="fas fa-user"></i> Nombre:</strong> <span>{{ $facturacion['nombre'] ?? '—' }}</span></li>
                        <li><strong><i class="fas fa-home"></i> Dirección fiscal:</strong> <span>{{ $facturacion['direccion'] ?? '—' }}</span></li>
                    </ul>
                </div>
            @endif

            {{-- Asignar Repartidor (demo UI) --}}
            <div class="repartidor-section no-print">
                <h4><i class="fas fa-motorcycle"></i> Asignar Repartidor</h4>
                <div class="repartidor-select">
                    <select class="form-select" id="repartidor-select">
                        <option value="">Seleccionar repartidor</option>
                        <option value="vendedor">Yo mismo (Vendedor)</option>
                        <option value="1">Juan Pérez (+502 5555-1234)</option>
                        <option value="2">María García (+502 5555-5678)</option>
                        <option value="3">Carlos López (+502 5555-9012)</option>
                    </select>
                    <button class="btn-vino" onclick="asignarRepartidor()">
                        <i class="fas fa-user-check"></i> Asignar
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Productos asignados --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-boxes"></i> Productos asignados</h3>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Estado</th>
                    <th class="no-print">Acción</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pedidoItems as $item)
                    @php
                        $estadoItem = $item->fulfillment_status ?? 'accepted';
                        $badgeItem = [
                            'accepted'=>'badge-aceptado',
                            'preparing'=>'badge-preparando',
                            'ready'=>'badge-listo',
                            'delivered'=>'badge-entregado',
                            'rejected'=>'badge-rechazado',
                        ][$estadoItem] ?? 'badge-pendiente';
                        $precio   = (float)($item->precio_unitario ?? 0);
                        $cantidad = (int)($item->cantidad ?? 0);
                        $sku      = optional($item->producto)->slug;
                    @endphp
                    <tr>
                        <td>
                            <div class="producto-info">
                                <span class="producto-nombre">{{ optional($item->producto)->nombre ?? '—' }}</span>
                                <span class="producto-meta">Ref: {{ $sku ?: '—' }}</span>
                            </div>
                        </td>
                        <td>{{ $cantidad }}</td>
                        <td>Q{{ number_format($precio, 2) }}</td>
                        <td>Q{{ number_format($cantidad * $precio, 2) }}</td>
                        <td><span class="badge-estado {{ $badgeItem }}">{{ $estadoItem }}</span></td>
                        <td class="no-print">
                            <form class="estado-form" action="{{ route('vendedor.pedidoitems.estado', $item) }}" method="POST">
                                @csrf
                                <select name="estado" class="form-select" onchange="this.form.submit()">
                                    <option value="accepted"   @selected($estadoItem==='accepted')>Aceptado</option>
                                    <option value="preparing"  @selected($estadoItem==='preparing')>Preparando</option>
                                    <option value="ready"      @selected($estadoItem==='ready')>Listo</option>
                                    <option value="delivered"  @selected($estadoItem==='delivered')>Entregado</option>
                                    <option value="rejected"   @selected($estadoItem==='rejected')>Rechazado</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Sin productos en este pedido.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($pedido)
            <div class="acciones-rapidas no-print">
                <form action="{{ route('vendedor.pedidoitems.actualizar.todos', $pedido) }}" method="POST">
                    @csrf
                    <input type="hidden" name="estado" value="preparing">
                    <button type="submit" class="btn-accion">
                        <i class="fas fa-clock"></i> Marcar todos en preparación
                    </button>
                </form>

                <form action="{{ route('vendedor.pedidoitems.actualizar.todos', $pedido) }}" method="POST">
                    @csrf
                    <input type="hidden" name="estado" value="ready">
                    <button type="submit" class="btn-accion">
                        <i class="fas fa-check"></i> Marcar todos listos
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    function asignarRepartidor() {
        const select = document.getElementById('repartidor-select');
        if (!select.value) { alert('Por favor selecciona un repartidor'); return; }
        alert('Repartidor asignado (demo). Implementa el POST real aquí.');
    }

    // Spinner al enviar formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            this.querySelectorAll('button').forEach(btn => {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                btn.disabled = true;
            });
        });
    });
</script>
</body>
</html>
