@extends('layouts.app')

@section('content')
    <h1 style="padding: 20px;">Listado de Categorías</h1>

    <!-- Botón de regresar -->
    <div style="padding: 0 20px 20px;">
        <a href="{{ route('admin.panel') }}"
           style="display: inline-block; padding: 10px 20px; background-color: #6c757d; color: #fff; text-decoration: none; border-radius: 6px; transition: background-color 0.3s;">
            ← Regresar al dashboard
        </a>
    </div>

    <!-- Botón de crear -->
    <div style="padding: 0 20px 20px;">
        <a href="{{ route('admin.categorias.create') }}"
           style="display: inline-block; padding: 12px 24px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 6px; transition: background-color 0.3s;">
            + Crear nueva categoría
        </a>
    </div>

    <!-- Tabla de categorías -->
    <table border="1" cellpadding="10" cellspacing="0"
           style="width: 100%; border-collapse: collapse; background-color: #fff; border-radius: 8px; overflow: hidden;">
        <thead style="background-color: #f5f5f5;">
        <tr>
            <th style="padding: 12px;">ID</th>
            <th style="padding: 12px;">Nombre</th>
            <th style="padding: 12px;">Descripción</th>
            <th style="padding: 12px;">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($categorias as $categoria)
            <tr style="border-top: 1px solid #ddd;">
                <td style="padding: 12px; text-align: center;">{{ $categoria->id }}</td>
                <td style="padding: 12px;">{{ $categoria->nombre }}</td>
                <td style="padding: 12px;">{{ $categoria->descripcion }}</td>
                <td style="padding: 12px; text-align: center;">
                    <a href="{{ route('admin.categorias.edit', $categoria->id) }}"
                       style="color: #007bff; text-decoration: none; margin-right: 10px;">Editar</a>
                    <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST"
                          style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('¿Estás seguro de eliminar esta categoría?')"
                                style="color: #dc3545; background: none; border: none; cursor: pointer;">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
