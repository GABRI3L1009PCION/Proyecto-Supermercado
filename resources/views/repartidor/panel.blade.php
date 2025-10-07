@extends('layouts.app')

@section('content')
    <style>
        .repartidor-dashboard{padding:24px;background:#f5f6f8;min-height:100vh}
        .rd-header h1{font-size:28px;font-weight:800;color:#1f2937;margin-bottom:4px}
        .rd-header p{color:#6b7280;margin:0 0 20px}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px}
        .stat-card{background:#fff;border-radius:14px;padding:16px;border:1px solid #e5e7eb;box-shadow:0 4px 12px rgba(15,23,42,0.06)}
        .stat-card span{display:block;font-size:14px;color:#6b7280;margin-bottom:6px}
        .stat-card strong{font-size:28px;color:#111827}
        .section-title{display:flex;align-items:center;justify-content:space-between;margin:24px 0 12px;color:#1f2937}
        .section-title h2{font-size:20px;font-weight:700;margin:0}
        .section-title small{color:#6b7280}
        .delivery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px}
        .delivery-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;box-shadow:0 6px 16px rgba(15,23,42,0.08);padding:18px;display:flex;flex-direction:column;gap:14px}
        .delivery-header{display:flex;justify-content:space-between;align-items:center}
        .delivery-code{font-weight:700;color:#4b5563;font-size:15px}
        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em}
        .badge-pending{background:#fef3c7;color:#92400e}
        .badge-preparing{background:#ede9fe;color:#5b21b6}
        .badge-ready{background:#dcfce7;color:#166534}
        .badge-delivered{background:#e0f2fe;color:#075985}
        .delivery-body p{margin:0 0 6px;font-size:14px;color:#374151}
        .delivery-body p strong{color:#111827}
        .delivery-actions{display:flex;flex-wrap:wrap;gap:10px}
        .btn{border:none;border-radius:10px;padding:10px 16px;font-weight:600;cursor:pointer;transition:.2s;font-size:14px;display:inline-flex;align-items:center;gap:8px}
        .btn-map{background:#e0f2fe;color:#075985}
        .btn-deliver{background:#16a34a;color:#fff}
        .btn-disabled{background:#f3f4f6;color:#9ca3af;cursor:not-allowed}
        .empty-state{background:#fff;border-radius:16px;padding:32px;text-align:center;color:#6b7280;border:1px dashed #d1d5db}
        .alert{padding:14px 18px;border-radius:12px;margin-bottom:18px;font-weight:600}
        .alert-success{background:#ecfdf5;color:#047857;border:1px solid #34d399}
        .alert-error{background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5}
        .history-list{background:#fff;border-radius:16px;border:1px solid #e5e7eb;box-shadow:0 4px 14px rgba(15,23,42,0.05);padding:16px}
        .history-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:14px;color:#4b5563}
        .history-item:last-child{border-bottom:none}
        .history-item strong{color:#111827}
        @media(max-width:640px){
            .repartidor-dashboard{padding:16px}
            .delivery-grid{grid-template-columns:1fr}
        }
    </style>

    <div class="repartidor-dashboard">
        <div class="rd-header">
            <h1>🚚 Mis entregas</h1>
            <p>Gestiona las entregas asignadas y confirma cuando hayas completado cada una.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span>Pendientes</span>
                <strong>{{ $stats['pendientes'] ?? 0 }}</strong>
            </div>
            <div class="stat-card">
                <span>Listas para entregar</span>
                <strong>{{ $stats['listos'] ?? 0 }}</strong>
            </div>
            <div class="stat-card">
                <span>Entregadas</span>
                <strong>{{ $stats['entregados'] ?? 0 }}</strong>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="section-title">
            <h2>Entregas activas</h2>
            <small>{{ ($items->count() ?? 0) }} en curso</small>
        </div>

        @if($items->isEmpty())
            <div class="empty-state">
                <strong>No tienes entregas activas.</strong>
                <p class="mt-2">Cuando un vendedor o el supermercado te asignen un pedido aparecerá aquí.</p>
            </div>
        @else
            <div class="delivery-grid">
                @foreach($items as $item)
                    @php
                        $pedido = $item->pedido;
                        $cliente = optional($pedido)->cliente;
                        $direccion = $pedido->direccion_envio ?? [];
                        if (is_string($direccion)) {
                            $direccion = json_decode($direccion, true) ?: [];
                        }
                        $lat = data_get($direccion, 'lat');
                        $lng = data_get($direccion, 'lng');
                        $mapLink = ($lat && $lng) ? sprintf('https://www.google.com/maps?q=%s,%s', $lat, $lng) : null;
                        $direccionTexto = $pedido?->direccion_formateada ?? (data_get($direccion, 'descripcion') ?? 'Dirección no disponible');
                        $status = $item->fulfillment_status;
                        $metodoPago = $pedido?->metodo_pago ? ucfirst($pedido->metodo_pago) : 'Efectivo';
                        $totalPedido = (float) ($pedido?->total ?? 0);
                        $badgeClass = [
                            'accepted'  => 'badge badge-pending',
                            'preparing' => 'badge badge-preparing',
                            'ready'     => 'badge badge-ready',
                            'delivered' => 'badge badge-delivered',
                        ][$status] ?? 'badge badge-pending';
                    @endphp
                    <div class="delivery-card">
                        <div class="delivery-header">
                            <span class="delivery-code">{{ $pedido?->codigo ?? ('PED-'.$item->pedido_id) }}</span>
                            <span class="{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                        </div>
                        <div class="delivery-body">
                            <p><strong>Cliente:</strong> {{ $cliente?->name ?? '—' }}</p>
                            @if($cliente?->telefono)
                                <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                            @endif
                            <p><strong>Producto:</strong> {{ optional($item->producto)->nombre ?? 'Producto' }} × {{ $item->cantidad }}</p>
                            @if($item->pickup_address)
                                <p><strong>Punto de recogida:</strong> {{ $item->pickup_address }}</p>
                            @endif
                            @if($item->pickup_contact || $item->pickup_phone)
                                <p><strong>Contacto:</strong> {{ $item->pickup_contact }} {{ $item->pickup_phone ? '· '.$item->pickup_phone : '' }}</p>
                            @endif
                            @if($item->delivery_instructions)
                                <p><strong>Indicaciones:</strong> {{ $item->delivery_instructions }}</p>
                            @endif
                            <p><strong>Dirección:</strong> {{ $direccionTexto }}</p>
                            <p><strong>Método de pago:</strong> {{ $metodoPago }}</p>
                            <p><strong>Total del pedido:</strong> Q{{ number_format($totalPedido, 2) }}</p>
                            <p><strong>Tarifa de entrega:</strong> Q{{ number_format((float)$item->delivery_fee, 2) }}</p>
                        </div>
                        <div class="delivery-actions">
                            @if($mapLink)
                                <a class="btn btn-map" href="{{ $mapLink }}" target="_blank" rel="noopener">
                                    <i class="fas fa-map-marker-alt"></i> Ver en mapa
                                </a>
                            @endif
                            @if($item->fulfillment_status === \App\Models\PedidoItem::ESTADO_LISTO)
                                <form method="POST" action="{{ route('repartidor.items.entregar', $item->id) }}">
                                    @csrf
                                    <button class="btn btn-deliver" type="submit">
                                        <i class="fas fa-check"></i> Marcar entregada
                                    </button>
                                </form>
                            @else
                                <span class="btn btn-disabled">
                                    <i class="fas fa-hourglass-half"></i> Esperando preparación
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="section-title" style="margin-top:32px;">
            <h2>Historial reciente</h2>
            <small>Últimas entregas confirmadas</small>
        </div>

        @if(($historial->count() ?? 0) === 0)
            <div class="empty-state">
                <strong>Todavía no registras entregas completadas.</strong>
                <p class="mt-2">Cuando marques entregas como completadas aparecerán aquí para tu control.</p>
            </div>
        @else
            <div class="history-list">
                @foreach($historial as $item)
                    @php
                        $pedido = $item->pedido;
                        $cliente = optional($pedido)->cliente;
                        $fecha = optional($item->updated_at)->format('d/m H:i');
                    @endphp
                    <div class="history-item">
                        <div>
                            <strong>{{ $pedido?->codigo ?? ('PED-'.$item->pedido_id) }}</strong>
                            <span> · {{ $cliente?->name ?? 'Cliente' }}</span>
                        </div>
                        <div>{{ $fecha }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
