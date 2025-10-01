@extends('layouts.app')

@section('content')
    <div style="padding:16px">
        @if(session('ok'))
            <div style="background:#e6fffa;border:1px solid #99f6e4;padding:10px;border-radius:6px;margin-bottom:10px">{{ session('ok') }}</div>
        @endif

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <h2 style="margin:0">Vendedores</h2>
            <a href="{{ route('admin.vendedores.create') }}" style="text-decoration:none;background:#16a34a;color:#fff;padding:8px 12px;border-radius:6px">Nuevo / Promover</a>
        </div>

        <div style="background:#fff;padding:12px;border:1px solid #ddd;border-radius:8px;overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;min-width:720px">
                <thead>
                <tr style="background:#f9fafb">
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">ID</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Usuario</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Email</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Estado</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Modo</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Comisión</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($vendors as $v)
                    <tr>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">{{ $v->id }}</td>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">{{ $v->user->name }}</td>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">{{ $v->user->email }}</td>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">{{ $v->status }}</td>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">{{ $v->pricing_mode }}</td>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">{{ number_format($v->commission_rate,2) }}%</td>
                        <td style="padding:8px;border-bottom:1px solid #f1f5f9">
                            <form method="POST" action="{{ route('admin.vendedores.toggle',$v) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button
                                    style="background:{{ $v->status==='active' ? '#f59e0b' : '#16a34a' }};color:#fff;border:none;padding:6px 10px;border-radius:6px;cursor:pointer">
                                    {{ $v->status==='active' ? 'Suspender' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:10px;text-align:center;color:#6b7280">No hay vendedores registrados.</td></tr>
                @endforelse
                </tbody>
            </table>

            <div style="margin-top:10px">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
@endsection

