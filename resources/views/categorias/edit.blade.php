@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto p-6 bg-white rounded-xl shadow mt-10">
        <h2 class="text-2xl font-bold mb-6">Editar Categoría</h2>

        <form action="{{ route('categorias.update', $categoria) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold">Nombre</label>
                <input type="text" name="nombre" value="{{ $categoria->nombre }}" class="w-full border px-4 py-2 rounded-xl" required>
            </div>

            <div>
                <label class="block font-semibold">Descripción</label>
                <textarea name="descripcion" class="w-full border px-4 py-2 rounded-xl">{{ $categoria->descripcion }}</textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-700">
                    Actualizar categoría
                </button>
            </div>
        </form>
    </div>
@endsection

