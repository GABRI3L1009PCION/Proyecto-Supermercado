{{-- resources/views/vendedor/productos/form.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        $mode = $mode ?? 'create';
        $isEdit = $mode === 'edit';
        $formAction = $isEdit
            ? route('vendedor.productos.update', $producto)
            : route('vendedor.productos.store');
    @endphp

    <div class="box">
        <h2>{{ $isEdit ? 'Editar producto' : 'Nuevo producto' }}</h2>

        @if ($errors->any())
            <div class="alert">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="form">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div class="grid">
                <label>
                    <span>Nombre</span>
                    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                </label>

                <label>
                    <span>Precio</span>
                    <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" required>
                </label>

                <label>
                    <span>Stock</span>
                    <input type="number" name="stock" value="{{ old('stock', $producto->stock) }}" required>
                </label>

                <label>
                    <span>Categoría</span>
                    <select name="categoria_id" required>
                        <option value="">-- Selecciona --</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" @selected(old('categoria_id', $producto->categoria_id)==$c->id)>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="col-2">
                    <span>Descripción</span>
                    <textarea name="descripcion" rows="5">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </label>

                <label class="col-2">
                    <span>Imagen principal</span>
                    <input type="file" name="imagen" accept="image/*" id="fileImagen">
                    <div class="preview">
                        @php
                            $imagenActual = $producto->imagen ? asset('storage/'.$producto->imagen) : null;
                        @endphp
                        <img id="imgPreview" alt="preview" style="{{ $imagenActual ? '' : 'display:none;' }}max-height:160px;border-radius:8px;" src="{{ $imagenActual }}">
                    </div>
                    <small>Formatos: jpg, png, webp. Máx 2MB.</small>
                    @if($isEdit && $producto->imagen)
                        <small>Actualmente: {{ $producto->imagen }}</small>
                    @endif
                </label>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Actualizar' : 'Crear' }}</button>
                <a class="btn" href="{{ route('vendedor.productos.index') }}">Cancelar</a>
            </div>
        </form>
    </div>

    <style>
        .box{max-width:900px;margin:0 auto;padding:16px 18px;background:#fff;border:1px solid #e5e7eb;border-radius:12px}
        h2{margin:0 0 12px}
        .form .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .form label{display:flex;flex-direction:column;gap:6px}
        .form label.col-2{grid-column:1 / -1}
        .form input, .form select, .form textarea{
            padding:10px;border:1px solid #e5e7eb;border-radius:10px
        }
        .form small.helper{color:#6b7280;font-size:12px;margin-top:4px}
        .actions{margin-top:14px;display:flex;gap:8px}
        .btn{padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;text-decoration:none;color:#111}
        .btn-primary{background:#16a34a;border-color:#16a34a;color:#fff}
        .alert{background:#fef2f2;border:1px solid #fecaca;padding:10px;border-radius:10px;margin-bottom:10px}
        .preview{margin-top:6px}
        @media (max-width:720px){.form .grid{grid-template-columns:1fr}}
    </style>

    <script>
        const file = document.getElementById('fileImagen');
        const img  = document.getElementById('imgPreview');
        file?.addEventListener('change', (e) => {
            const f = e.target.files?.[0];
            if (!f) { img.style.display='none'; return; }
            const url = URL.createObjectURL(f);
            img.src = url;
            img.style.display = 'block';
        });
    </script>
@endsection
