@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl shadow-md mt-10">
        <h2 class="text-2xl font-bold mb-6">Editar Producto</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold">Nombre</label>
                <input type="text" name="nombre" value="{{ $producto->nombre }}" class="w-full border px-4 py-2 rounded-xl" required>
            </div>

            <div>
                <label class="block font-semibold">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full border px-4 py-2 rounded-xl">{{ $producto->descripcion }}</textarea>
            </div>

            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block font-semibold">Precio</label>
                    <input type="number" step="0.01" name="precio" value="{{ $producto->precio }}" class="w-full border px-4 py-2 rounded-xl" required>
                </div>
                <div class="w-1/2">
                    <label class="block font-semibold">Stock</label>
                    <input type="number" name="stock" value="{{ $producto->stock }}" class="w-full border px-4 py-2 rounded-xl" required>
                </div>
            </div>

            <div>
                <label class="block font-semibold">Imagen actual:</label>
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" class="h-24 mb-3 rounded">
                @else
                    <p class="text-gray-500">Sin imagen</p>
                @endif
                <input type="file" name="imagen" class="w-full">
            </div>

            <div class="text-right">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-700">
                    Actualizar producto
                </button>
            </div>
        </form>
    </div>
@endsection
