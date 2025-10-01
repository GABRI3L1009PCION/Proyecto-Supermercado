<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        // Consolidar fuentes (controller y/o sesión)
        $pid  = $pid  ?? $pedidoId     ?? session('pedido_id')      ?? session('ultimo_pedido_id');
        $pcod = $pcod ?? $pedidoCodigo ?? session('pedido_codigo')  ?? session('ultimo_pedido_codigo');

        // Fallbacks seguros para <title>
        $pidTitulo  = $pid  ?: 'SIN-ID';
        $pcodTitulo = $pcod ?: ('PED-'.$pidTitulo);
    @endphp

    <title>Seguimiento de Pedido - {{ $pcodTitulo }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --vino-primario: #722F37;
            --vino-secundario: #8C3A44;
            --vino-terciario: #A64D57;
            --vino-claro: #F9F2F3;
            --vino-acento: #D9A6A6;
            --texto-oscuro: #2C181A;
            --texto-claro: #FFFFFF;
            --ok: #16a34a;
            --warn: #f59e0b;
            --muted: #6b7280;
        }
        body { background-color:#F8F9FA; color:var(--texto-oscuro); font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; padding:20px 0; }
        .track-wrap { max-width:960px; margin:24px auto; padding:25px; background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        .hdr { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:20px; padding-bottom:15px; border-bottom:2px solid var(--vino-claro); }
        .code { font-weight:800; font-size:1.5rem; color:var(--vino-primario); }
        .badge { padding:.5rem 1rem; border-radius:999px; font-weight:700; font-size:.9rem; background:var(--vino-claro); color:var(--vino-primario); text-transform:capitalize; }
        .grid { display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-top:25px; }
        @media (max-width:900px){ .grid{ grid-template-columns:1fr; } }
        .card { border:1px solid #eef2f7; border-radius:12px; padding:20px; background:#fff; box-shadow:0 4px 12px rgba(114,47,55,.1); }
        .title { font-weight:800; margin-bottom:12px; color:var(--vino-primario); font-size:1.1rem; }
        .progress-container { display:flex; justify-content:space-between; position:relative; margin:25px 0; padding:0 10px; }
        .step { text-align:center; position:relative; flex:1; }
        .step-circle { width:26px; height:26px; margin:auto; border-radius:50%; background-color:var(--vino-claro); border:3px solid #ddd; z-index:2; position:relative; transition:all .4s ease; }
        .step.active .step-circle { background-color:var(--vino-primario); border-color:var(--vino-primario); box-shadow:0 0 0 4px rgba(114,47,55,.2); }
        .step.completed .step-circle { background-color:var(--vino-secundario); border-color:var(--vino-secundario); }
        .step.completed .step-circle::after { content:'✓'; color:#fff; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-weight:bold; }
        .step::after { content:''; position:absolute; top:12px; left:50%; height:4px; width:100%; background-color:#ddd; z-index:1; transform:translateX(-50%); transition:background-color .4s ease; }
        .step:last-child::after{ display:none; }
        .step.active::after, .step.completed::after { background-color:var(--vino-secundario); }
        .step-label { display:block; margin-top:10px; font-size:.85rem; color:#666; text-transform:capitalize; font-weight:500; transition:color .3s ease; }
        .step.active .step-label, .step.completed .step-label { color:var(--vino-primario); font-weight:600; }
        .driver { display:flex; gap:15px; align-items:center; }
        .driver img { width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid var(--vino-acento); padding:2px; }
        .muted { color:var(--muted); font-size:.9rem; }
        .big { font-size:1.5rem; font-weight:800; color:var(--vino-primario); }
        .row { display:flex; justify-content:space-between; gap:15px; margin-top:20px; padding-top:15px; border-top:1px solid var(--vino-claro); }
        .btn-vino { background-color:var(--vino-primario); color:#fff; border:none; padding:10px 20px; border-radius:8px; font-weight:600; transition:all .3s ease; }
        .btn-vino:hover { background-color:var(--vino-secundario); transform:translateY(-2px); box-shadow:0 4px 8px rgba(114,47,55,.3); }
        .btn-link { color:var(--vino-primario); text-decoration:none; background:none; border:none; padding:10px 0; font-weight:600; }
        .btn-link:hover { color:var(--vino-secundario); text-decoration:underline; }
        .productos-list { margin-top:20px; background-color:var(--vino-claro); border-radius:8px; padding:15px; }
        .producto-item { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--vino-acento); }
        .producto-item:last-child{ border-bottom:none; }
        .error-box { background-color:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin:15px 0; border-left:4px solid #dc3545; }
        .loading { display:inline-block; width:20px; height:20px; border:3px solid rgba(114,47,55,.3); border-radius:50%; border-top-color:var(--vino-primario); animation:spin 1s ease-in-out infinite; }
        @keyframes spin { to{ transform:rotate(360deg); } }
        @media (max-width:768px){
            .progress-container{ flex-direction:column; align-items:flex-start; gap:25px; margin-left:20px; }
            .step{ display:flex; align-items:center; width:100%; }
            .step::after{ top:50%; left:13px; width:4px; height:100%; transform:translateY(-50%); }
            .step-circle{ margin:0 15px 0 0; }
            .step-label{ margin-top:0; }
            .row{ flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="track-wrap">
        @php
            // Bandera opcional
            $done = $pedidoRealizado ?? session('pedido_realizado', false);
        @endphp

        @if(empty($pid))
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle text-warning fa-3x mb-3"></i>
                <h4>No encontramos tu pedido reciente</h4>
                <p class="muted">Parece que no hay información de pedidos disponibles.</p>
                <a class="btn btn-vino mt-3" href="{{ route('cliente.productos') }}">
                    <i class="fas fa-arrow-left me-2"></i>Volver al catálogo
                </a>
            </div>
        @else
            <div class="hdr">
                <div>
                    <div class="code">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Pedido {{ $pcod ?: ('PED-'.($pid ?: 'SIN-ID')) }}
                    </div>
                    <div class="muted">Gracias por tu compra. Aquí puedes ver el estado en tiempo real.</div>
                </div>
                <span id="estado-badge" class="badge">Cargando…</span>
            </div>

            <div id="error-message" class="error-box d-none">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="error-text"></span>
            </div>

            <div class="grid">
                <div class="card">
                    <div class="title"><i class="fas fa-tasks me-2"></i>Progreso del Pedido</div>

                    <div class="progress-container" id="progress">
                        @php $labels = ['pendiente', 'preparando', 'listo', 'entregado']; @endphp
                        @foreach($labels as $label)
                            <div class="step" data-etapa="{{ $label }}">
                                <div class="step-circle"></div>
                                <span class="step-label">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="productos-list">
                        <div class="title"><i class="fas fa-box-open me-2"></i>Productos</div>
                        <div id="productos-lista">
                            <p class="text-center muted">Cargando productos...</p>
                        </div>
                    </div>

                    <div class="row">
                        <div>
                            <div class="title"><i class="fas fa-map-marker-alt me-2"></i>Dirección de entrega</div>
                            <div id="dir" class="muted">Cargando...</div>
                        </div>
                        <div class="text-end">
                            <div class="title"><i class="fas fa-receipt me-2"></i>Total</div>
                            <div id="total" class="big">Q 0.00</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="title"><i class="fas fa-motorcycle me-2"></i>Repartidor</div>
                    <div class="driver" id="driver">
                        <img id="driver-foto" src="{{ asset('images/avatar.png') }}" alt="Repartidor">
                        <div>
                            <div id="driver-nombre" class="fw-semibold">Aún sin asignar</div>
                            <div class="muted"><span id="driver-tel">—</span></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="title"><i class="fas fa-info-circle me-2"></i>Información del Pedido</div>
                        <div class="muted">
                            <p class="mb-1">Código: <span id="pedido-codigo">{{ $pcod ?: ('PED-'.($pid ?: 'SIN-ID')) }}</span></p>
                            <p class="mb-1">Estado: <span id="estado-global">Cargando...</span></p>
                            <p class="mb-0">Última actualización: <span id="fecha-actualizacion">—</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a class="btn-link" href="{{ route('cliente.productos') }}">
                    <i class="fas fa-arrow-left me-1"></i> Volver al catálogo
                </a>
                <button class="btn btn-vino" id="btn-refresh">
                    <i class="fas fa-sync-alt me-2"></i>Actualizar ahora
                </button>
            </div>
        @endif
    </div>
</div>

@if(!empty($pid))
    <script>
        const pedidoId = {{ (int)$pid }};
        const url = "{{ route('cliente.estado.json', ':id') }}".replace(':id', pedidoId);

        const stepsOrder = ['pendiente', 'preparando', 'listo', 'entregado'];
        const money = v => 'Q ' + Number(v||0).toFixed(2);

        function mostrarError(mensaje){
            const box = document.getElementById('error-message');
            const txt = document.getElementById('error-text');
            txt.textContent = mensaje;
            box.classList.remove('d-none');
            setTimeout(()=>box.classList.add('d-none'), 5000);
        }

        function setBadge(estado){
            const badge = document.getElementById('estado-badge');
            if(estado){ badge.textContent = estado.replace('_',' ').toUpperCase(); }
        }

        function paintProgress(estado){
            const idx = stepsOrder.indexOf(estado);
            document.querySelectorAll('#progress .step').forEach((step, i) => {
                step.classList.remove('active','completed');
                if (i < idx) step.classList.add('completed');
                else if (i === idx) step.classList.add('active');
            });
        }

        async function fetchEstado(){
            try{
                const btn = document.getElementById('btn-refresh');
                btn.disabled = true;
                btn.innerHTML = '<span class="loading"></span> Actualizando...';

                const resp = await fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' } });
                if(!resp.ok) throw new Error('Error '+resp.status+': '+resp.statusText);
                const data = await resp.json();

                setBadge(data.estado);
                paintProgress(data.estado);

                document.getElementById('dir').textContent   = data.direccion || 'Dirección no especificada';
                document.getElementById('total').textContent = money(data.total);

                // Repartidor
                if (data.repartidor && data.repartidor.nombre){
                    document.getElementById('driver-nombre').textContent = data.repartidor.nombre;
                    document.getElementById('driver-tel').textContent    = data.repartidor.telefono ? ('Tel: '+data.repartidor.telefono) : '—';
                    if (data.repartidor.foto) document.getElementById('driver-foto').src = data.repartidor.foto;
                } else {
                    document.getElementById('driver-nombre').textContent = 'Aún sin asignar';
                    document.getElementById('driver-tel').textContent = '—';
                }

                // Productos
                const cont = document.getElementById('productos-lista');
                if (data.productos && data.productos.length){
                    cont.innerHTML = data.productos.map(p => `
                    <div class="producto-item">
                        <span>${p.cantidad}x ${p.nombre}</span>
                        <span>Q ${(p.precio * p.cantidad).toFixed(2)}</span>
                    </div>
                `).join('');
                } else {
                    cont.innerHTML = '<p class="text-center muted">No se encontraron productos</p>';
                }

                document.getElementById('estado-global').textContent = data.estado || 'pendiente';
                if (data.updated_at){
                    document.getElementById('fecha-actualizacion').textContent =
                        new Date(data.updated_at).toLocaleString();
                }

                if (data.estado === 'entregado' && window.__poll){
                    clearInterval(window.__poll);
                    window.__poll = null;
                }
            }catch(err){
                console.error(err);
                mostrarError('No se pudo cargar el estado del pedido: ' + err.message);
            }finally{
                const btn = document.getElementById('btn-refresh');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Actualizar ahora';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btn-refresh').addEventListener('click', fetchEstado);
            fetchEstado();
            window.__poll = setInterval(fetchEstado, 30000);
        });
    </script>
@endif
</body>
</html>
