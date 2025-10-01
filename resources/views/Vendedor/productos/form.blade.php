{{-- resources/views/vendedor/productos/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="box">
        <h2>Nuevo producto</h2>

        @if ($errors->any())
            <div class="alert">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('vendedor.productos.store') }}" enctype="multipart/form-data" class="form">
            @csrf
            <div class="grid">
                <label>
                    <span>Nombre</span>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                </label>

                <label>
                    <span>Precio</span>
                    <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" required>
                </label>

                <label>
                    <span>Stock</span>
                    <input type="number" name="stock" value="{{ old('stock') }}" required>
                </label>

                <label>
                    <span>Categoría</span>
                    <select name="categoria_id" required>
                        <option value="">-- Selecciona --</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" @selected(old('categoria_id')==$c->id)>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="col-2">
                    <span>Descripción</span>
                    <textarea name="descripcion" rows="5">{{ old('descripcion') }}</textarea>
                </label>

                <label class="col-2">
                    <span>Imagen principal</span>
                    <input type="file" name="imagen" accept="image/*" id="fileImagen">
                    <div class="preview">
                        <img id="imgPreview" alt="preview" style="display:none;max-height:160px;border-radius:8px;">
                    </div>
                    <small>Formatos: jpg, png, webp. Máx 2MB.</small>
                </label>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Crear</button>
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
