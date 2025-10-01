@extends('layouts.app')

@section('content')
    <div class="cat-wrap">

        {{-- Flash de éxito --}}
        @if(session('ok'))
            <div class="alert ok">
                {{ session('ok') }}
            </div>
        @endif

        {{-- Header + Acciones --}}
        <div class="cat-header">
            <h1>Categorías</h1>
            <div class="cat-actions">
                <a href="{{ url()->previous() }}" class="btn btn-back">⬅ Regresar</a>
                <a href="{{ route('admin.categorias.create') }}" class="btn btn-create">+ Nueva categoría</a>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('admin.categorias.index') }}" class="cat-filters card">
            <div class="filters-grid">
                <div class="fg">
                    <label for="q">Buscar</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="inp"
                        placeholder="Nombre o descripción…"
                        value="{{ request('q') }}"
                    >
                </div>

                <div class="fg">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="inp">
                        <option value="">— Todos —</option>
                        <option value="activo"   {{ request('estado')==='activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('estado')==='inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="fg fg-actions">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="{{ route('admin.categorias.index') }}" class="btn btn-muted">Limpiar</a>
                </div>
            </div>
        </form>

        {{-- Tabla --}}
        <div class="cat-table-box">
            <div class="table-scroll">
                <table class="cat-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categorias as $c)
                        <tr>
                            <td>#{{ $c->id }}</td>
                            <td>{{ $c->nombre }}</td>
                            <td title="{{ $c->descripcion }}">
                                {{ \Illuminate\Support\Str::limit($c->descripcion ?? '—', 70) }}
                            </td>
                            <td>
                                    <span class="badge {{ $c->estado === 'activo' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $c->estado }}
                                    </span>
                            </td>
                            <td>{{ $c->created_at?->format('Y-m-d') }}</td>
                            <td class="td-actions">
                                <a href="{{ route('admin.categorias.edit', $c->id) }}" class="btn btn-edit">✏️ Editar</a>

                                <form action="{{ route('admin.categorias.destroy', $c->id) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta categoría?');" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">No hay categorías.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación (mantiene filtros) --}}
        
    </div>

    {{-- CSS --}}
    <style>
        /* Wrapper */
        .cat-wrap{max-width:1100px;margin:0 auto;padding:20px;color:#1f2937;font-family:'Segoe UI',Tahoma,Arial,sans-serif}

        /* Alert */
        .alert.ok{
            background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;
            padding:10px 12px;border-radius:10px;margin-bottom:12px;font-weight:600
        }

        /* Header */
        .cat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
        .cat-header h1{margin:0;font-size:26px;font-weight:800}

        /* Buttons */
        .btn{display:inline-block;padding:10px 14px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;transition:all .2s ease}
        .btn:hover{transform:translateY(-1px)}
        .btn-back{background:#6b7280;color:#fff}
        .btn-back:hover{background:#4b5563}
        .btn-create{background:#16a34a;color:#fff}
        .btn-create:hover{background:#15803d}
        .btn-primary{background:#2563eb;color:#fff;border:0}
        .btn-primary:hover{background:#1d4ed8}
        .btn-muted{background:#f3f4f6;color:#111827}
        .btn-muted:hover{background:#e5e7eb}
        .btn-edit{background:#3b82f6;color:#fff}
        .btn-edit:hover{background:#2563eb}
        .btn-delete{background:#dc2626;color:#fff;border:none;cursor:pointer}
        .btn-delete:hover{background:#b91c1c}

        /* Cards / filtros */
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:14px}
        .cat-filters{margin-bottom:12px}
        .filters-grid{display:grid;grid-template-columns:2fr 1fr auto;gap:12px}
        .fg{display:flex;flex-direction:column;gap:6px}
        .fg-actions{align-self:end;display:flex;gap:8px}
        label{font-size:13px;color:#374151}
        .inp{width:100%;padding:11px 12px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
        .inp:focus{border-color:#93c5fd;box-shadow:0 0 0 3px rgba(147,197,253,.35)}

        /* Tabla */
        .cat-table-box{background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,.05)}
        .table-scroll{overflow:auto}
        .cat-table{width:100%;border-collapse:collapse;font-size:14px;min-width:880px}
        .cat-table thead tr{background:#f9fafb}
        .cat-table th,.cat-table td{padding:12px;text-align:left;border-bottom:1px solid #f0f0f0}
        .cat-table tbody tr:hover{background:#f9fafc}
        .col-actions{width:220px}
        .td-actions{display:flex;gap:8px;align-items:center}

        /* Badges */
        .badge{padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.2px;text-transform:capitalize}
        .badge-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
        .badge-danger{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}

        /* Empty & pagination */
        .empty{text-align:center;padding:16px;color:#6b7280}
        .pagination{margin-top:14px}

        /* Responsive */
        @media (max-width:900px){
            .filters-grid{grid-template-columns:1fr 1fr;gap:10px}
            .fg-actions{grid-column:1/-1;justify-content:flex-start}
        }
        @media (max-width:620px){
            .cat-header{flex-direction:column;align-items:flex-start;gap:10px}
        }
    </style>
@endsection
