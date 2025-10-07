@extends('layouts.app')

@section('content')
    <div class="container-editar">
        <h1>✏️ Editar Producto</h1>

        <form method="POST" action="{{ route('admin.productos.update', $producto->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label>Nombre:</label>
            <input type="text" name="nombre" value="{{ $producto->nombre }}" required>

            <label>Descripción:</label>
            <textarea name="descripcion" required>{{ $producto->descripcion }}</textarea>

            <label>Categoría:</label>
            <select name="categoria_id" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>

            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="{{ $producto->precio }}" required>

            <label>Tarifa de entrega (Q):</label>
            <input type="number" step="0.01" min="0" max="500" name="delivery_price" value="{{ old('delivery_price', $producto->delivery_price) }}">
            <small style="color:#6b7280;display:block;margin-top:4px;">Costo de envío aplicado cuando el supermercado gestiona la entrega de este producto.</small>

            <label>Stock:</label>
            <input type="number" name="stock" value="{{ $producto->stock }}" required>

            <label>Imagen:</label>
            <input type="file" name="imagen">
            @if($producto->imagen)
                <div class="preview-img">
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual">
                </div>
            @endif

            <label>Estado:</label>
            <select name="estado" required>
                <option value="activo" {{ $producto->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ $producto->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>

            <div class="form-actions">
                <a href="{{ route('admin.productos.index') }}" class="btn-cancelar">Cancelar</a>
                <button type="submit">Actualizar producto</button>
            </div>
        </form>
    </div>

    <style>
        .container-editar {
            max-width: 700px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 3px 16px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #800020;
            text-align: center;
            margin-bottom: 2rem;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #800020;
            margin-top: 1rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .preview-img {
            margin-top: 0.5rem;
        }

        .preview-img img {
            max-height: 100px;
            border-radius: 6px;
        }

        .form-actions {
            text-align: center;
            margin-top: 2rem;
        }

        .form-actions button,
        .form-actions .btn-cancelar {
            display: inline-block;
            margin: 0 0.5rem;
            padding: 0.9rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .form-actions button {
            background: #800020;
            color: #fff;
            border: none;
        }

        .form-actions button:hover {
            background: #5a0017;
        }

        .form-actions .btn-cancelar {
            background: #ccc;
            color: #333;
        }

        .form-actions .btn-cancelar:hover {
            background: #999;
            color: #fff;
        }
    </style>
@endsection
