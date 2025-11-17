@extends('layouts.app')

@section('title', 'Zonas de reparto | Panel de vendedor')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Mis zonas de reparto</h1>
            <p class="text-muted mb-0">Configura tus propias coberturas y tarifas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('vendedor.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </a>
            <a href="{{ route('vendedor.zonas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva zona
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="d-none d-md-table-cell">Cobertura</th>
                            <th>Tarifa (Q)</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                            <tr>
                                <td>{{ $zone->nombre }}</td>
                                <td class="d-none d-md-table-cell">
                                    {{ $zone->descripcion_cobertura ? \Illuminate\Support\Str::limit($zone->descripcion_cobertura, 80) : '—' }}
                                </td>
                                <td>Q{{ number_format($zone->tarifa_reparto, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $zone->activa ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $zone->activa ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('vendedor.zonas.edit', $zone) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('vendedor.zonas.destroy', $zone) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar la zona "{{ $zone->nombre }}"?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Aún no has registrado zonas. Empieza creando una para controlar tus tarifas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($zones->hasPages())
            <div class="card-footer">
                {{ $zones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
