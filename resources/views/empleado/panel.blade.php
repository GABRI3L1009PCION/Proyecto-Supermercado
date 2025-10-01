@extends('layouts.app')

@section('content')
    <div class="dashboard-container" style="display:flex;">
        <style>
            .main{flex:1;padding:20px;background:#f5f6f8}
            .h1{font-size:22px;font-weight:700;margin-bottom:14px}
            .card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px}
            table{width:100%;border-collapse:collapse;font-size:14px}
            th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
            .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;border:1px solid currentColor}
            .b-accepted{color:#6b7280}.b-preparing{color:#2563eb}.b-ready{color:#16a34a}
            .actions form{display:inline-block}
            .btn{border:0;border-radius:8px;padding:8px 10px;font-weight:600;cursor:pointer}
            .btn-blue{background:#2563eb;color:#fff}
            .btn-green{background:#16a34a;color:#fff}
            .muted{color:#6b7280}
            .topbar{display:flex;gap:10px;align-items:center;margin-bottom:12px}
            .topbar input[type="search"]{padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;width:260px}
        </style>

        <main class="main">
            <div class="h1">📦 Pedidos a preparar (Súper)</div>

            <div class="card">
                <div class="topbar">
                    <form method="GET">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por cliente o pedido...">
                    </form>
                    <div class="muted">Mostrando {{ $items->count() }} de {{ $items->total() }} ítems</div>
                </div>

                <div class="table-wrap" style="overflow-x:auto">
                    <table>
                        <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Estado</th>
                            <th style="width:220px">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $it)
                            @php
                                $badge = ['accepted'=>'b-accepted','preparing'=>'b-preparing','ready'=>'b-ready'][$it->fulfillment_status] ?? 'b-accepted';
                            @endphp
                            <tr>
                                <td>#{{ $it->pedido_id }}</td>
                                <td>{{ optional($it->pedido->cliente)->name ?? '—' }}</td>
                                <td>{{ optional($it->producto)->nombre ?? '—' }}</td>
                                <td>{{ $it->cantidad }}</td>
                                <td><span class="badge {{ $badge }}">{{ $it->fulfillment_status }}</span></td>
                                <td class="actions">
                                    @if($it->fulfillment_status === 'accepted')
                                        <form method="POST" action="{{ route('empleado.items.preparar', $it->id) }}">@csrf
                                            <button class="btn btn-blue">Marcar preparando</button>
                                        </form>
                                    @endif
                                    @if(in_array($it->fulfillment_status, ['accepted','preparing']))
                                        <form method="POST" action="{{ route('empleado.items.listo', $it->id) }}">@csrf
                                            <button class="btn btn-green">Marcar listo</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="muted">No hay ítems pendientes.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:10px">
                    {{ $items->withQueryString()->links() }}
                </div>
            </div>
        </main>
    </div>
@endsection

