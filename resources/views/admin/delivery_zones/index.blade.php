@extends('layouts.app')

@section('content')
    <style>
        .zones-wrapper{padding:24px;background:#f5f6f8;min-height:100vh}
        .zones-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px}
        .zones-header h1{font-size:28px;font-weight:800;color:#1f2937;margin:0}
        .btn-primary{background:#2563eb;color:#fff;padding:10px 16px;border-radius:10px;border:none;text-decoration:none;font-weig
ht:600}
        .btn-primary:hover{background:#1d4ed8}
        .zones-card{background:#fff;border-radius:16px;padding:18px;border:1px solid #e5e7eb;box-shadow:0 6px 16px rgba(15,23,42,
0.08)}
        table{width:100%;border-collapse:collapse}
        th,td{padding:12px 10px;text-align:left;border-bottom:1px solid #f1f5f9;font-size:14px}
        th{background:#f8fafc;color:#1f2937;font-weight:700}
        tbody tr:hover{background:#f9fafb}
        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:60
0}
        .badge-active{background:#dcfce7;color:#166534}
        .badge-inactive{background:#fee2e2;color:#b91c1c}
        .actions{display:flex;gap:8px}
        .btn{padding:8px 12px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;border:1px solid #e5e7eb;colo
r:#1f2937;background:#fff}
        .btn:hover{background:#f1f5f9}
        .alert{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-weight:600}
        .alert-success{background:#ecfdf5;color:#047857;border:1px solid #34d399}
        .pagination{margin-top:16px;display:flex;justify-content:flex-end}
        .pagination ul{display:flex;gap:6px;list-style:none;padding:0;margin:0}
        .pagination a{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;borde
r:1px solid #e5e7eb;color:#1f2937;text-decoration:none;font-size:13px;font-weight:600}
        .pagination a:hover{background:#f1f5f9}
        .pagination .active span{background:#2563eb;color:#fff;border-color:#2563eb}
    </style>

    <div class="zones-wrapper">
        <div class="zones-header">
            <h1>Zonas de entrega</h1>
            <a class="btn-primary" href="{{ route('admin.delivery-zones.create') }}">Añadir zona</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="zones-card">
            <table>
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Municipio</th>
                        <th>Tarifa base</th>
                        <th>Coordenadas</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                        <tr>
                            <td>{{ $zone->nombre }}</td>
                            <td>{{ $zone->municipio }}</td>
                            <td>Q{{ number_format((float)$zone->tarifa_base, 2) }}</td>
                            <td>
                                @if($zone->lat && $zone->lng)
                                    {{ $zone->lat }}, {{ $zone->lng }}
                                @else
                                    <span class="text-muted">Sin coordenadas</span>
                                @endif
                            </td>
                            <td>
                                @if($zone->activo)
                                    <span class="badge badge-active">Activa</span>
                                @else
                                    <span class="badge badge-inactive">Inactiva</span>
                                @endif
                            </td>
                            <td class="actions">
                                <a class="btn" href="{{ route('admin.delivery-zones.edit', $zone) }}">Editar</a>
                                <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}" onsubmit="return c
onfirm('¿Eliminar la zona seleccionada?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:24px;color:#6b7280;">Aún no se han registrado zonas de entreg
a.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $zones->links() }}
            </div>
        </div>
    </div>
@endsection
