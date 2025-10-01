@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">Categorías</h2>

        <a href="{{ route('categorias.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 mb-4 inline-block">Nueva Categoría</a>

        <table class="w-full border bg-white rounded-xl shadow overflow-hidden">
            <thead class="bg-gray-100 text-gray-800">
            <tr>
                <th class="p-3 text-left">Nombre</th>
                <th class="p-3 text-left">Descripción</th>
                <th class="p-3 text-left">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categorias as $categoria)
                <tr class="border-t">
                    <td class="p-3">{{ $categoria->nombre }}</td>
                    <td class="p-3">{{ $categoria->descripcion }}</td>
                    <td class="p-3 space-x-2">
                        <a href="{{ route('categorias.edit', $categoria) }}" class="text-blue-600 hover:underline">Editar</a>
                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

