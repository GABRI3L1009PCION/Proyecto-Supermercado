@extends('layouts.app')

@section('content')

    <!-- Contenedor del formulario -->
    <form method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data"
          style="max-width: 750px; margin: 0 auto 40px; padding: 30px; border-radius: 16px; background-color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
        @csrf

        <!-- Título dentro del formulario -->
        <h1 style="text-align: center; color: #800020; font-size: 2rem; font-weight: bold; margin-bottom: 30px;">
            🛒 Crear Producto
        </h1>

        <div style="display: flex; flex-wrap: wrap; gap: 20px;">

            <!-- Nombre -->
            <div style="flex: 1 1 100%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Nombre:</label>
                <input type="text" name="nombre" required
                       style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <!-- Descripción -->
            <div style="flex: 1 1 100%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Descripción:</label>
                <textarea name="descripcion" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;"></textarea>
            </div>

            <!-- Categoría -->
            <div style="flex: 1 1 48%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Categoría:</label>
                <select name="categoria_id" required
                        style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;">
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Precio -->
            <div style="flex: 1 1 48%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Precio (Q):</label>
                <input type="number" step="0.01" name="precio" required
                       style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <!-- Stock -->
            <div style="flex: 1 1 48%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Stock:</label>
                <input type="number" name="stock" required
                       style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <!-- Imagen -->
            <div style="flex: 1 1 48%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Imagen:</label>
                <input type="file" name="imagen"
                       style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <!-- Estado -->
            <div style="flex: 1 1 100%;">
                <label style="display: block; font-weight: bold; color: #800020; margin-bottom: 6px;">Estado:</label>
                <select name="estado" required
                        style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px;">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <!-- Botones: guardar y regresar -->
        <div style="text-align: center; margin-top: 30px;">
            <button type="submit"
                    style="background-color: #800020; color: #fff; padding: 14px 32px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-right: 20px;">
                Guardar producto
            </button>

            <a href="{{ route('admin.productos.index') }}"
               style="background-color: #6c757d; color: #fff; padding: 14px 24px; border-radius: 8px; text-decoration: none; font-size: 16px;">
                ← Volver al listado
            </a>
        </div>
    </form>
@endsection
