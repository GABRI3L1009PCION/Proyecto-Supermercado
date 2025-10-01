@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl shadow-md mt-10">
        <h2 class="text-2xl font-bold mb-6">Crear Producto</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block font-semibold">Nombre</label>
                <input type="text" name="nombre" class="w-full border px-4 py-2 rounded-xl" required>
            </div>

            <div>
                <label class="block font-semibold">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full border px-4 py-2 rounded-xl"></textarea>
            </div>

            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block font-semibold">Precio</label>
                    <input type="number" step="0.01" name="precio" class="w-full border px-4 py-2 rounded-xl" required>
                </div>
                <div class="w-1/2">
                    <label class="block font-semibold">Stock</label>
                    <input type="number" name="stock" class="w-full border px-4 py-2 rounded-xl" required>
                </div>
            </div>

            <div>
                <label class="block font-semibold">Imagen (opcional)</label>
                <input type="file" name="imagen" class="w-full">
            </div>

            <div class="text-right">
                <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-xl hover:bg-green-700">
                    Guardar producto
                </button>
            </div>
        </form>
    </div>
@endsection

