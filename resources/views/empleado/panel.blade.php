@extends('layouts.app')

@section('title', 'Panel del Empleado | Atlantia Supermarket')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --vino: #4A052A;
            --vino-claro: #751A3D;
            --rosa: #C25B9A;
            --gris-fondo: #f7f8fa;
            --texto: #333;
            --blanco: #fff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gris-fondo);
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ====== HEADER / SUMMARY CARDS ====== */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: var(--blanco);
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }

        .summary-card h3 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-card p {
            font-size: 22px;
            font-weight: 700;
            color: var(--vino);
        }

        /* ====== MAIN CONTENT ====== */
        .main {
            flex: 1;
            padding: 25px;
            max-width: 1300px;
            margin: auto;
        }

        .main h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--vino);
            margin-bottom: 25px;
        }

        /* ====== TABLE CARD ====== */
        .card {
            background: var(--blanco);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border: 1px solid #ececec;
        }

        .topbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 10px;
        }

        .topbar input[type="search"], .topbar select {
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        .topbar input[type="search"]:focus, .topbar select:focus {
            border-color: var(--vino-claro);
            box-shadow: 0 0 0 2px rgba(117,26,61,0.15);
        }

        .topbar .muted {
            color: #6b7280;
            font-size: 14px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: var(--vino);
            color: var(--blanco);
            font-weight: 500;
        }

        tr:hover {
            background: #fafafa;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .b-accepted { background: #f3f4f6; color: #6b7280; }
        .b-preparing { background: #dbeafe; color: #1d4ed8; }
        .b-ready { background: #dcfce7; color: #16a34a; }

        .actions form {
            display: inline-block;
            margin-right: 6px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 13px;
        }

        .btn-blue {
            background: var(--vino);
            color: var(--blanco);
        }

        .btn-blue:hover {
            background: var(--vino-claro);
        }

        .btn-green {
            background: #16a34a;
            color: var(--blanco);
        }

        .btn-green:hover {
            background: #15803d;
        }

        .btn-gray {
            background: #e5e7eb;
            color: #374151;
        }
        .btn-gray:hover {
            background: #d1d5db;
        }

        /* ====== CLIENT CONSULTATION ====== */
        .consultas {
            margin-top: 35px;
        }

        .consulta {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            margin-bottom: 10px;
        }

        .consulta h4 {
            color: var(--vino);
            margin-bottom: 8px;
        }

        .consulta p {
            color: #444;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .consulta small {
            color: #6b7280;
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 992px) {
            .main {
                padding: 15px;
            }

            table {
                font-size: 13px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .summary-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        @media (max-width: 600px) {
            th, td {
                font-size: 12px;
                padding: 8px;
            }
            .btn {
                padding: 6px 8px;
                font-size: 12px;
            }
        }
    </style>

    <div class="dashboard-container">
        <main class="main">

            <h1>🧑‍🍳 Panel del Empleado - Atlantia Supermarket</h1>

            <!-- ====== RESUMEN GENERAL ====== -->
            <div class="summary-cards">
                <div class="summary-card">
                    <h3>Pedidos totales</h3>
                    <p>{{ $totalPedidos ?? 128 }}</p>
                </div>
                <div class="summary-card">
                    <h3>En preparación</h3>
                    <p>{{ $preparando ?? 35 }}</p>
                </div>
                <div class="summary-card">
                    <h3>Listos para entrega</h3>
                    <p>{{ $listos ?? 22 }}</p>
                </div>
                <div class="summary-card">
                    <h3>Consultas clientes</h3>
                    <p>{{ $consultas ?? 5 }}</p>
                </div>
            </div>

            <!-- ====== PEDIDOS ====== -->
            <div class="card">
                <div class="topbar">
                    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por cliente o pedido...">
                        <select name="estado" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="accepted" {{ request('estado')=='accepted'?'selected':'' }}>Aceptado</option>
                            <option value="preparing" {{ request('estado')=='preparing'?'selected':'' }}>Preparando</option>
                            <option value="ready" {{ request('estado')=='ready'?'selected':'' }}>Listo</option>
                        </select>
                    </form>

                    <div class="btn-group">
                        <a href="{{ route('empleado.dashboard') }}" class="btn btn-gray">🔄 Actualizar</a>
                        <a href="#consultas" class="btn btn-blue">💬 Ver Consultas</a>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $it)
                            @php
                                $badge = [
                                    'accepted' => 'b-accepted',
                                    'preparing' => 'b-preparing',
                                    'ready' => 'b-ready'
                                ][$it->fulfillment_status] ?? 'b-accepted';
                            @endphp
                            <tr>
                                <td>#{{ $it->pedido_id }}</td>
                                <td>{{ optional($it->pedido->cliente)->name ?? '—' }}</td>
                                <td>{{ optional($it->producto)->nombre ?? '—' }}</td>
                                <td>{{ $it->cantidad }}</td>
                                <td><span class="badge {{ $badge }}">{{ ucfirst($it->fulfillment_status) }}</span></td>
                                <td class="actions">
                                    @if($it->fulfillment_status === 'accepted')
                                        <form method="POST" action="{{ route('empleado.items.preparar', $it->id) }}">@csrf
                                            <button class="btn btn-blue">Preparando</button>
                                        </form>
                                    @endif
                                    @if(in_array($it->fulfillment_status, ['accepted','preparing']))
                                        <form method="POST" action="{{ route('empleado.items.listo', $it->id) }}">@csrf
                                            <button class="btn btn-green">Listo</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="muted" style="text-align:center;padding:20px;">No hay pedidos pendientes.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:15px;">
                    {{ $items->withQueryString()->links() }}
                </div>
            </div>

            <!-- ====== CONSULTAS CLIENTES ====== -->
            <div id="consultas" class="consultas">
                <h2 style="color:var(--vino);margin:20px 0 10px;">💬 Consultas recientes de clientes</h2>

                @forelse($consultasClientes ?? [] as $c)
                    <div class="consulta">
                        <h4>{{ $c->cliente->name ?? 'Cliente desconocido' }}</h4>
                        <p>{{ $c->mensaje }}</p>
                        <small>{{ $c->created_at->diffForHumans() }}</small>

                        <form method="POST" action="{{ route('empleado.consultas.responder', $c->id) }}" style="margin-top:10px;">
                            @csrf
                            <input type="text" name="respuesta" placeholder="Escribe una respuesta..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:8px;">
                            <button class="btn btn-blue" style="margin-top:8px;">Responder</button>
                        </form>
                    </div>
                @empty
                    <p class="muted">No hay consultas pendientes de clientes.</p>
                @endforelse
            </div>
        </main>
    </div>
@endsection
