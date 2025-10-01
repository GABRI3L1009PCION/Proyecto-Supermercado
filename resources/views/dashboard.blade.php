@extends('layouts.app')

@section('content')
    <div class="dashboard-container" style="display:flex;">
        <style>
            .main{flex:1;padding:20px;background:#f5f6f8}
            .h1{font-size:22px;font-weight:700;margin-bottom:14px}
            .kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:12px}
            .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px}
            .label{color:#6b7280;font-size:12px}
            .value{font-size:22px;font-weight:700}
            .table-wrap{overflow-x:auto}
            table{width:100%;border-collapse:collapse;font-size:14px;min-width:720px}
            th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
            .actions form{display:inline-block;margin-right:6px}
            .btn{border:0;border-radius:8px;padding:7px 9px;font-weight:600;cursor:pointer}
            .b1{background:#64748b;color:#fff}  /* accepted */
            .b2{background:#2563eb;color:#fff}  /* preparing */
            .b3{background:#16a34a;color:#fff}  /* ready */
            .b4{background:#059669;color:#fff}  /* delivered */
            .b5{background:#ef4444;color:#fff}  /* rejected */
            @media (max-width:900px){ .kpis{grid-template-columns:repeat(2,1fr)} }
        </style>

        <main class="main">
            <div class="h1">🛍️ Panel del Vendedor</div>

            <section class="kpis">
                <div class="card"><div class="label">Mis productos</div><div class="value">{{ $misProductos ?? 0 }}</div></div>
                <div class="card"><div class="label">Pendientes</div><div class="value">{{ $kpis['pendientes'] ?? 0 }}</div></div>
                <div class="card"><div class="label">Listos</div><div class="value">{{ $kpis['listos'] ?? 0 }}</div></div>
                <div class="card"><div class="label">Entregados</div><div class="value">{{ $kpis['entregados'] ?? 0 }}</div></div>
            </section>

            <div class="card table-wrap">
                <h2 style="margin-top:0">Mis pedidos (ítems)</h2>
                <table>
                    <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Estado</th>
                        <th style="width:420px">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items ?? [] as $it)
                        <tr>
                            <td>#{{ $it->pedido_id }}</td>
                            <td>{{ optional($it->pedido->cliente)->name ?? '—' }}</td>
                            <td>{{ optional($it->producto)->nombre ?? '—' }}</td>
                            <td>{{ $it->cantidad }}</td>
                            <td>{{ $it->fulfillment_status }}</td>
                            <td class="actions">
                                @foreach(['accepted'=>'b1','preparing'=>'b2','ready'=>'b3','delivered'=>'b4','rejected'=>'b5'] as $accion => $cls)
                                    <form method="POST" action="{{ route('vendedor.surtido.estado', ['item'=>$it->id,'accion'=>$accion]) }}">
                                        @csrf
                                        <button class="btn {{ $cls }}" @if($it->fulfillment_status === $accion) disabled @endif>
                                            {{ ucfirst($accion) }}
                                        </button>
                                    </form>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No hay ítems para mostrar.</td></tr>
                    @endforelse
                    </tbody>
                </table>

                @if(method_exists($items,'links'))
                    <div style="margin-top:10px">{{ $items->withQueryString()->links() }}</div>
                @endif
            </div>
        </main>
    </div>
@endsection
