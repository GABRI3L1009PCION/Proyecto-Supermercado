@extends('layouts.app')

@section('content')
    <div class="dashboard-container" style="display:flex;">
        <style>
            .main{flex:1;padding:20px;background:#f5f6f8}
            .h1{font-size:22px;font-weight:700;margin-bottom:14px}
            .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:14px}
            .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px}
            .row{display:flex;justify-content:space-between;gap:10px;margin:6px 0}
            .muted{color:#6b7280}
            .btn{border:0;border-radius:8px;padding:8px 10px;font-weight:600;cursor:pointer}
            .btn-green{background:#16a34a;color:#fff}
            .pill{display:inline-block;padding:4px 10px;border-radius:999px;border:1px solid #16a34a;color:#16a34a;font-size:12px}
        </style>

        <main class="main">
            <div class="h1">🚚 Mis entregas</div>

            @if($items->isEmpty())
                <div class="card muted">No tienes entregas pendientes.</div>
            @else
                <div class="grid">
                    @foreach($items as $it)
                        <div class="card">
                            <div class="row"><strong>Pedido</strong> <span>#{{ $it->pedido_id }}</span></div>
                            <div class="row"><strong>Cliente</strong> <span>{{ optional($it->pedido->cliente)->name ?? '—' }}</span></div>
                            <div class="row"><strong>Producto</strong> <span>{{ optional($it->producto)->nombre ?? '—' }} × {{ $it->cantidad }}</span></div>
                            <div class="row"><strong>Dirección</strong> <span class="muted">{{ $it->pedido->direccion_envio['descripcion'] ?? '—' }}</span></div>
                            <div class="row"><strong>Estado</strong> <span class="pill">{{ $it->fulfillment_status }}</span></div>

                            @if(in_array($it->fulfillment_status,['ready','preparing']))
                                <form method="POST" action="{{ route('repartidor.items.entregar',$it->id) }}" style="margin-top:8px">
                                    @csrf
                                    <button class="btn btn-green">Marcar entregado</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
@endsection
