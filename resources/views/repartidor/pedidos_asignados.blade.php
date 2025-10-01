@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffe6f0;
        }

        .titulo {
            color: #800020;
            font-weight: bold;
        }

        .pedido-card {
            background-color: #800020;
            border: 2px solid #ffcc66;
            border-radius: 16px;
            padding: 15px 20px;
            margin: 20px auto;
            color: white;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            max-width: 380px;
        }

        .pedido-info p {
            margin: 3px 0;
            font-size: 15px;
        }

        .estado-badge {
            background-color: #ffcc66;
            color: #800020;
            font-weight: bold;
            border-radius: 12px;
            padding: 5px 10px;
            display: inline-block;
            margin-top: 8px;
        }

        .pedido-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-confirmar, .btn-rechazar, .btn-rastreo, .btn-regresar, .btn-aceptar {
            font-weight: bold;
            border-radius: 8px;
            padding: 6px 12px;
            border: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-confirmar {
            background-color: #28a745;
            color: white;
        }

        .btn-rechazar {
            background-color: #dc3545;
            color: white;
        }

        .btn-rastreo {
            background-color: #ffc107;
            color: #800020;
        }

        .btn-rastreo.disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        .btn-aceptar {
            background-color: #17a2b8;
            color: white;
        }

        .btn-regresar {
            background-color: #6c757d;
            color: white;
            margin: 0 auto 20px auto;
            display: block;
            max-width: 200px;
        }

        .modal .modal-content {
            border-radius: 12px;
        }

        .modal-title {
            color: #800020;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .pedido-card {
                margin: 15px 10px;
                padding: 15px;
            }

            .pedido-actions {
                flex-direction: column;
            }

            .btn-confirmar, .btn-rechazar, .btn-rastreo, .btn-aceptar {
                width: 100%;
            }
        }
    </style>

    <div class="container mt-4">
        <h2 class="text-center titulo mb-4">📦 Pedidos Asignados</h2>

        <a href="{{ route('repartidor.panel') }}" class="btn btn-regresar">
            ← Regresar al Panel
        </a>

        @if($pedidos->count())
            @foreach ($pedidos as $pedido)
                <div class="pedido-card">
                    <h5>Pedido #{{ $pedido->id }}</h5>

                    <div class="pedido-info">
                        <p><i class="fas fa-user"></i> Cliente: {{ optional($pedido->cliente)->nombre ?? 'Sin nombre' }}</p>
                        <p><i class="fas fa-calendar-day"></i> Fecha: {{ $pedido->created_at->format('d/m/Y') }}</p>
                        <p><i class="fas fa-money-bill-wave"></i> Total: Q{{ number_format($pedido->total, 2) }}</p>
                        @if($pedido->direccion)
                            <p><i class="fas fa-map-marker-alt"></i> Dirección: {{ $pedido->direccion }}</p>
                        @endif
                    </div>

                    <span class="estado-badge">
                    <i class="fas fa-truck"></i> {{ ucfirst($pedido->estado) }}
                </span>

                    <div class="pedido-actions">
                        @if($pedido->estado === 'asignado')
                            <form action="{{ route('repartidor.pedido.aceptar', $pedido->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-aceptar">
                                    <i class="fas fa-handshake"></i> Aceptar pedido
                                </button>
                            </form>
                        @endif

                        @if($pedido->estado === 'aceptado')
                            <form action="{{ route('repartidor.pedido.confirmar', $pedido->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-confirmar">
                                    <i class="fas fa-check-circle"></i> Confirmar entrega
                                </button>
                            </form>
                        @endif

                        <!-- Ver ubicación -->
                        <a href="{{ $pedido->direccion
                                ? 'https://www.google.com/maps/dir/?api=1&origin=Mi+Ubicacion&destination=' . urlencode($pedido->direccion)
                                : '#' }}"
                           target="_blank"
                           class="btn-rastreo {{ $pedido->direccion ? '' : 'disabled' }}"
                           title="{{ $pedido->direccion ? 'Ver ruta en Google Maps' : 'Dirección no disponible' }}">
                            <i class="fas fa-route"></i> Ver ubicación
                        </a>

                        <!-- Rechazar -->
                        @if(in_array($pedido->estado, ['asignado', 'aceptado']))
                            <button type="button" class="btn-rechazar" onclick="rechazarPedido({{ $pedido->id }})">
                                <i class="fas fa-times-circle"></i> Rechazar
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-warning text-center mt-4">
                <i class="fas fa-info-circle"></i> No tienes pedidos asignados actualmente.
            </div>
        @endif
    </div>

    <!-- Modal Rechazo -->
    <div class="modal fade" id="modalRechazo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formRechazo" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rechazar pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <label for="justificacion">Motivo del rechazo:</label>
                        <textarea name="justificacion" class="form-control" required rows="3"
                                  placeholder="Ejemplo: Cliente no se encontraba, dirección incorrecta, etc."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Enviar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function rechazarPedido(id) {
            const form = document.getElementById('formRechazo');
            form.action = `/repartidor/pedidos/${id}/rechazar`;
            const modal = new bootstrap.Modal(document.getElementById('modalRechazo'));
            modal.show();
        }
    </script>
@endsection
