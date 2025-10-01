<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión de Pedidos | Supermercado Atlantia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        @media (max-width:768px){.estados{grid-template-columns:1fr}.filtros{overflow-x:auto;padding-bottom:1rem}.stats-container{flex-direction:column;align-items:center}.stat-card{width:100%;max-width:300px}header{flex-direction:column;text-align:center}.header-left{flex-direction:column}.btn-back{align-self:flex-start}.modal-content{width:95%;margin:1rem}}
        @media (max-width:480px){body{padding:.5rem}header{padding:1rem}h1{font-size:1.5rem}.pedido-acciones{flex-direction:column}.btn{width:100%;justify-content:center}.modal-footer{flex-direction:column}}
    </style>
</head>
<body>

@php
    // Plantilla segura de URL para el form del modal (evita hardcodear)
    $asignarUrlTemplate = route('admin.pedidos.asignar-repartidor', ['pedido' => '__ID__']);
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
                @forelse(($pedidos ?? collect())->where('estado','pendiente') as $pedido)
                    <div class="pedido">
                        <div class="pedido-id">
                            <span>ID: {{ $pedido->id }}</span>
                            @if(!empty($pedido->urgente))<span class="badge badge-urgente">Urgente</span>@endif
                        </div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>

                        @if(($pedido->productos ?? collect())->count())
                            <div class="pedido-productos">
                                @foreach($pedido->productos as $producto)
                                    <div class="producto-item">
                                        <span>{{ $producto->nombre }} x{{ $producto->pivot->cantidad ?? 1 }}</span>
                                        <span>Q{{ number_format(($producto->pivot->precio ?? $producto->precio) * ($producto->pivot->cantidad ?? 1), 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="pedido-acciones">
                            <button class="btn btn-asignar" onclick="abrirModalAsignar({{ $pedido->id }})">
                                <i class="fas fa-user-plus"></i> Asignar
                            </button>
                            <button class="btn btn-detalles" onclick="abrirModalDetalles({{ $pedido->id }})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
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
                @forelse(($pedidos ?? collect())->where('estado','preparando') as $pedido)
                    <div class="pedido pedido-preparando">
                        <div class="pedido-id"><span>ID: {{ $pedido->id }}</span></div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>

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
                @forelse(($pedidos ?? collect())->where('estado','listo') as $pedido)
                    <div class="pedido pedido-listo">
                        <div class="pedido-id"><span>ID: {{ $pedido->id }}</span></div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>

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
                @forelse(($pedidos ?? collect())->where('estado','entregado') as $pedido)
                    <div class="pedido pedido-entregado">
                        <div class="pedido-id"><span>ID: {{ $pedido->id }}</span></div>
                        <div class="pedido-cliente">{{ $pedido->cliente->name ?? 'Cliente no disponible' }}</div>
                        <div class="pedido-total">Q{{ number_format($pedido->total, 2) }}</div>
                        <div class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
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

<script>
    // Plantilla de ruta generada por Laravel (más robusto que hardcodear)
    const ASIGNAR_URL_TEMPLATE = @json($asignarUrlTemplate);

    function abrirModalAsignar(pedidoId) {
        document.getElementById('pedidoId').value = pedidoId;
        const form = document.getElementById('formAsignar');
        form.action = ASIGNAR_URL_TEMPLATE.replace('__ID__', pedidoId);
        document.getElementById('modalAsignar').style.display = 'flex';
    }

    function abrirModalDetalles(pedidoId) {
        document.getElementById('detallesContenido').innerHTML = '<p>Cargando detalles del pedido...</p>';
        document.getElementById('modalDetalles').style.display = 'flex';
        setTimeout(() => {
            document.getElementById('detallesContenido').innerHTML = `
                <div class="detalles-pedido">
                    <div class="detalle-item"><span class="detalle-label">ID del Pedido:</span><span>${pedidoId}</span></div>
                    <div class="detalle-item"><span class="detalle-label">Cliente:</span><span>Cliente #${pedidoId}</span></div>
                    <div class="detalle-item"><span class="detalle-label">Total:</span><span>Q${(pedidoId * 10).toFixed(2)}</span></div>
                    <div class="detalle-item"><span class="detalle-label">Estado:</span><span>Pendiente</span></div>
                    <div class="detalle-item"><span class="detalle-label">Fecha:</span><span>${new Date().toLocaleDateString()}</span></div>
                </div>
                <div class="lista-productos">
                    <h3>Productos</h3>
                    <div class="producto-detalle"><span>Producto 1 x2</span><span>Q20.00</span></div>
                    <div class="producto-detalle"><span>Producto 2 x1</span><span>Q15.50</span></div>
                </div>`;
        }, 400);
    }

    function cerrarModal(id){ document.getElementById(id).style.display='none'; }

    window.onclick = function(e){ if(e.target.classList.contains('modal')) e.target.style.display='none'; }

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
