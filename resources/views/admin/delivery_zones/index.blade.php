@extends('layouts.app')

@section('content')
    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator $zones */
        $draftZone = $newZone ?? new \App\Models\DeliveryZone(['activo' => true]);
    @endphp

    <style>
        .zones-wrapper { padding: 32px; background: #f5f6f8; min-height: 100vh; }
        .zones-header { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
        .zones-header h1 { font-size: 30px; font-weight: 800; color: #1f2937; margin: 0; }
        .zones-subtitle { color: #6b7280; font-size: 15px; margin: 4px 0 0; }
        .zones-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: flex-start; }
        .zones-card, .zone-form-card { background: #fff; border-radius: 16px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 6px 16px rgba(15,23,42,0.08); }
        .zones-card h2, .zone-form-card h2 { font-size: 20px; font-weight: 700; color: #1f2937; margin: 0 0 6px; }
        .zones-card p, .zone-form-card p { margin: 0 0 16px; color: #6b7280; font-size: 14px; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        th { background: #f8fafc; color: #1f2937; font-weight: 700; }
        tbody tr:hover { background: #f9fafb; }
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #b91c1c; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #e5e7eb; color: #1f2937; background: #fff; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { background: #f1f5f9; }
        .btn-danger { color: #b91c1c; border-color: #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .alert { padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-weight: 600; }
        .alert-success { background: #ecfdf5; color: #047857; border: 1px solid #34d399; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }
        .pagination { margin-top: 16px; display: flex; justify-content: flex-end; }
        .pagination ul { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; }
        .pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; border-radius: 8px; border: 1px solid #e5e7eb; color: #1f2937; text-decoration: none; font-size: 13px; font-weight: 600; padding: 0 8px; }
        .pagination a:hover { background: #f1f5f9; }
        .pagination .active span { background: #2563eb; color: #fff; border-color: #2563eb; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        label { display: flex; flex-direction: column; font-weight: 600; color: #1f2937; font-size: 14px; }
        input, select { margin-top: 6px; padding: 12px 14px; border-radius: 10px; border: 1px solid #d1d5db; font-size: 15px; background: #fff; color: #111827; }
        input:focus, select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
        .error-text { color: #b91c1c; font-size: 12px; font-weight: 500; margin-top: 6px; }
        .switch { display: flex; align-items: center; gap: 10px; margin: 20px 0 0; }
        .switch label { margin: 0; font-weight: 600; flex-direction: row; align-items: center; gap: 8px; font-size: 14px; }
        .form-actions { margin-top: 24px; display: flex; gap: 12px; }
        .btn-primary { background: #2563eb; color: #fff; padding: 12px 20px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { background: #1d4ed8; }
        .empty-state { padding: 24px; text-align: center; color: #6b7280; }
        @media (max-width: 1024px) { .zones-layout { grid-template-columns: 1fr; } }
    </style>

    <div class="zones-wrapper">
        <div class="zones-header">
            <div>
                <h1>Área de servicio</h1>
                <p class="zones-subtitle">Administra los barrios y colonias disponibles para entregas en Puerto Barrios y Santo Tomás de Castilla.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="zones-layout">
            <section class="zones-card">
                <h2>Zonas registradas</h2>
                <p>Consulta y administra las áreas en las que opera el supermercado.</p>

                <div class="table-wrapper">
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
                                    <td>Q{{ number_format((float) $zone->tarifa_base, 2) }}</td>
                                    <td>
                                        @if(!is_null($zone->lat) && !is_null($zone->lng))
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
                                        <a class="btn" href="{{ route('admin.delivery-zones.edit', $zone) }}"><i class="fas fa-pen"></i> Editar</a>
                                        <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}" onsubmit="return confirm('¿Eliminar la zona seleccionada?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">Aún no se han registrado zonas de entrega.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $zones->links() }}
                </div>
            </section>

            <section class="zone-form-card">
                <h2>Registrar barrio o colonia</h2>
                <p>Completa el formulario para añadir una nueva área de servicio.</p>

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.delivery-zones.store') }}">
                    @csrf

                    <div class="form-grid">
                        <label>
                            <span>Nombre de la zona</span>
                            <input type="text" name="nombre" value="{{ old('nombre', $draftZone->nombre) }}" placeholder="Ej. Barrio El Centro" required>
                            @error('nombre')<span class="error-text">{{ $message }}</span>@enderror
                        </label>

                        <label>
                            <span>Municipio</span>
                            <select name="municipio" required>
                                <option value="">Seleccione</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio }}" @selected(old('municipio', $draftZone->municipio) === $municipio)>{{ $municipio }}</option>
                                @endforeach
                            </select>
                            @error('municipio')<span class="error-text">{{ $message }}</span>@enderror
                        </label>

                        <label>
                            <span>Tarifa base (Q)</span>
                            <input type="number" step="0.01" min="0" max="500" name="tarifa_base" value="{{ old('tarifa_base', $draftZone->tarifa_base ?? 0) }}" required>
                            @error('tarifa_base')<span class="error-text">{{ $message }}</span>@enderror
                        </label>

                        <label>
                            <span>Latitud</span>
                            <input type="number" step="0.000001" name="lat" value="{{ old('lat', $draftZone->lat) }}" placeholder="15.7169">
                            @error('lat')<span class="error-text">{{ $message }}</span>@enderror
                        </label>

                        <label>
                            <span>Longitud</span>
                            <input type="number" step="0.000001" name="lng" value="{{ old('lng', $draftZone->lng) }}" placeholder="-88.5940">
                            @error('lng')<span class="error-text">{{ $message }}</span>@enderror
                        </label>
                    </div>

                    <div class="switch">
                        <input type="hidden" name="activo" value="0">
                        <label for="activo">
                            <input type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $draftZone->activo ?? true))>
                            Zona activa
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar zona</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
