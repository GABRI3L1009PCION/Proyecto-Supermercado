@extends('layouts.app')

@section('content')
    <div class="cat-wrap">
        <div class="cat-header">
            <div>
                <h1>Nueva categoría</h1>
                <p class="subtitle">Registra una categoría para organizar productos en el catálogo.</p>
            </div>
            <div class="cat-actions">
                <a href="{{ url()->previous() }}" class="btn btn-back">⬅ Regresar</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Oops…</strong> Hay errores en el formulario.
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categorias.store') }}" class="card">

        @csrf

            <div class="grid-2">
                <div class="form-group">
                    <label for="nombre">Nombre <span class="req">*</span></label>
                    <input type="text" id="nombre" name="nombre" class="inp" required
                           value="{{ old('nombre') }}" placeholder="Ej. Bebidas, Limpieza, Lácteos">
                </div>

                <div class="form-group">
                    <label for="estado">Estado <span class="req">*</span></label>
                    <select id="estado" name="estado" class="inp">
                        <option value="activo"   {{ old('estado','activo')==='activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado')==='inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción (opcional)</label>
                <textarea id="descripcion" name="descripcion" class="inp" rows="5"
                          placeholder="Breve descripción para uso interno o SEO.">{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">💾 Crear</button>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-muted">Cancelar</a>

            </div>
        </form>
    </div>

    <style>
        /* Wrapper */
        .cat-wrap{max-width:900px;margin:0 auto;padding:20px;color:#1f2937;font-family:'Segoe UI',Tahoma,Arial,sans-serif}
        /* Header */
        .cat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
        .cat-header h1{margin:0;font-size:26px;font-weight:800}
        .subtitle{margin:.25rem 0 0;color:#6b7280;font-size:14px}
        /* Buttons */
        .btn{display:inline-block;padding:10px 14px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;transition:all .2s ease}
        .btn:hover{transform:translateY(-1px)}
        .btn-back{background:#6b7280;color:#fff}
        .btn-back:hover{background:#4b5563}
        .btn-primary{background:#16a34a;color:#fff;border:0}
        .btn-primary:hover{background:#15803d}
        .btn-muted{background:#f3f4f6;color:#111827}
        .btn-muted:hover{background:#e5e7eb}
        /* Card */
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 16px;box-shadow:0 2px 8px rgba(0,0,0,.03)}
        /* Grid */
        .grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        @media (max-width:760px){.grid-2{grid-template-columns:1fr}}
        /* Form */
        .form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:12px}
        label{font-size:13px;color:#374151}
        .req{color:#ef4444}
        .inp{width:100%;padding:11px 12px;border:1px solid #e5e7eb;border-radius:10px;outline:none;background:#fff;transition:border-color .15s, box-shadow .15s}
        .inp:focus{border-color:#93c5fd;box-shadow:0 0 0 3px rgba(147,197,253,.35)}
        textarea.inp{resize:vertical}
        /* Actions */
        .form-actions{display:flex;gap:10px;margin-top:6px}
        /* Alerts */
        .alert{border-radius:12px;padding:12px 14px;margin-bottom:14px}
        .alert-error{background:#fef2f2;border:1px solid #fee2e2;color:#991b1b}
        .alert-error ul{margin:.4rem 0 0 1.1rem}
    </style>
@endsection
