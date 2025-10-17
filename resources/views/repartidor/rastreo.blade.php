@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <a href="{{ route('repartidor.panel') }}" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title">📍 Rastreo del pedido {{ $pedido->codigo ?? ('PED-' . $pedido->id) }}</h2>
                <p class="text-muted mb-4">Estado actual: <span class="badge bg-primary text-uppercase">{{ $pedido->estado }}</span></p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold">Datos del cliente</h5>
                        <p><i class="fas fa-user"></i> {{ optional($pedido->cliente)->name ?? 'Cliente no disponible' }}</p>
                        <p><i class="fas fa-phone"></i> {{ optional($pedido->cliente)->telefono ?? (data_get($pedido->direccion_envio, 'telefono') ?? 'Sin teléfono') }}</p>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold">Tiempos de entrega</h5>
                        <ul class="list-unstyled">
                            <li><strong>Asignado:</strong> {{ optional($pedido->fecha_asignado ?? $pedido->created_at)->format('d/m/Y H:i') }}</li>
                            <li><strong>Aceptado:</strong> {{ optional($pedido->fecha_aceptado)->format('d/m/Y H:i') ?? '—' }}</li>
                            <li><strong>Salida a ruta:</strong> {{ optional($pedido->fecha_salida)->format('d/m/Y H:i') ?? '—' }}</li>
                            <li><strong>Entregado:</strong> {{ optional($pedido->fecha_entregado)->format('d/m/Y H:i') ?? '—' }}</li>
                        </ul>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold">Detalle de productos</h5>
                @if($pedido->productos->isEmpty())
                    <p class="text-muted">No hay productos registrados en este pedido.</p>
                @else
                    <ul class="list-group mb-3">
                        @foreach($pedido->productos as $producto)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $producto->nombre }}</span>
                                <span class="badge bg-secondary">x{{ $producto->pivot->cantidad ?? 0 }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @php
                    $lat = $pedido->latitud_entrega ?? data_get($pedido->direccion_envio, 'lat');
                    $lng = $pedido->longitud_entrega ?? data_get($pedido->direccion_envio, 'lng');
                    $mapUrl = $lat && $lng
                        ? 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng
                        : ($pedido->direccion_formateada ? 'https://www.google.com/maps/dir/?api=1&origin=Mi+Ubicacion&destination=' . urlencode($pedido->direccion_formateada) : null);
                @endphp

                <div class="mt-4">
                    <h5 class="fw-bold">Seguimiento en mapa</h5>
                    @if($mapUrl)
                        <a href="{{ $mapUrl }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-map"></i> Ver en Google Maps
                        </a>
                    @else
                        <p class="text-muted">No hay coordenadas disponibles para este pedido.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
