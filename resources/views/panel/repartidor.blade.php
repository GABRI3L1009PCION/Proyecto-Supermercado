@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">

        <!-- Resumen General -->
        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <img src="{{ Auth::user()->foto ?? asset('img/default_driver.png') }}"
                         alt="Foto Repartidor" class="rounded-circle me-3" width="70" height="70">
                    <div>
                        <h4 class="mb-0">{{ Auth::user()->name }}</h4>
                        <span class="badge bg-success" id="estado-actual">Disponible</span>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="card shadow text-center p-2">
                        <span class="fw-bold text-primary">Pendientes</span>
                        <h4>{{ $pedidosPendientes ?? 0 }}</h4>
                    </div>
                    <div class="card shadow text-center p-2">
                        <span class="fw-bold text-warning">En Camino</span>
                        <h4>{{ $pedidosEnCamino ?? 0 }}</h4>
                    </div>
                    <div class="card shadow text-center p-2">
                        <span class="fw-bold text-success">Entregados Hoy</span>
                        <h4>{{ $pedidosEntregados ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sección de Pedidos Activos -->
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">📦 Pedidos Activos</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($pedidosActivos ?? [] as $pedido)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>#{{ $pedido->codigo }}</strong><br>
                                        <small>{{ $pedido->cliente }} - {{ $pedido->direccion_resumida }}</small><br>
                                        <span class="badge bg-warning">⏰ {{ $pedido->hora_limite ?? 'Prioridad' }}</span>
                                    </div>
                                    <button class="btn btn-sm btn-success">Iniciar</button>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">No tienes pedidos activos</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Detalle del Pedido Seleccionado -->
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-info text-white">📝 Detalle del Pedido</div>
                    <div class="card-body">
                        <p><strong>Código:</strong> <span id="detalle-codigo">--</span></p>
                        <p><strong>Cliente:</strong> <span id="detalle-cliente">--</span></p>
                        <p><strong>Dirección:</strong> <span id="detalle-direccion">--</span></p>
                        <p><strong>Monto a Cobrar:</strong> Q<span id="detalle-monto">0.00</span></p>

                        <hr>
                        <p class="fw-bold">Productos:</p>
                        <ul id="detalle-productos">
                            <li class="text-muted">Selecciona un pedido para ver detalles</li>
                        </ul>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button class="btn btn-sm btn-primary">Abrir en Google Maps</button>
                            <button class="btn btn-sm btn-warning">En Camino</button>
                            <button class="btn btn-sm btn-success">Entregado</button>
                            <button class="btn btn-sm btn-danger">Reportar Problema</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa Interactivo -->
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-warning text-dark">📍 Mapa en Tiempo Real</div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial y Métricas -->
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">📊 Historial de Entregas</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($historial ?? [] as $h)
                                <li class="list-group-item">
                                    <strong>#{{ $h->codigo }}</strong> - {{ $h->cliente }}
                                    <span class="float-end text-muted">{{ $h->fecha }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">No hay entregas registradas</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">📈 Métricas</div>
                    <div class="card-body">
                        <p>Pedidos Completados: <strong>{{ $pedidosEntregados ?? 0 }}</strong></p>
                        <p>Tiempo Promedio de Entrega: <strong>{{ $tiempoPromedio ?? '0 min' }}</strong></p>
                        <p>Kilómetros Recorridos: <strong>{{ $kmRecorridos ?? 0 }} km</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notificaciones y Perfil -->
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">🔔 Notificaciones</div>
                    <div class="card-body">
                        <ul id="lista-notificaciones">
                            <li class="text-muted">Sin notificaciones nuevas</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">⚙️ Perfil y Soporte</div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        <a href="{{ route('repartidor.perfil') }}" class="btn btn-primary w-100">Modificar Perfil</a>
                        <a href="{{ route('repartidor.soporte') }}" class="btn btn-warning w-100">Soporte / Chat</a>
                        <a href="{{ route('logout') }}" class="btn btn-danger w-100"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Cerrar Sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
