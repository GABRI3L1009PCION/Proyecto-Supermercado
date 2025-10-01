@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">🛍️ Productos disponibles</h1>

        @if ($productos->isEmpty())
            <div class="text-center text-gray-600 mt-12">
                <p class="text-lg">📭 No hay productos disponibles por ahora.</p>
                <p class="text-sm mt-2">Cuando el administrador los cargue, aparecerán aquí.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($productos as $producto)
                    <div class="bg-white shadow-md rounded-xl p-4 hover:shadow-xl transition duration-300">
                        {{-- Imagen con valor por defecto --}}
                        <img src="{{ asset('storage/' . ($producto->imagen ?? 'default.png')) }}"
                             alt="{{ $producto->nombre }}"
                             class="w-full h-40 object-cover rounded-md mb-3">

                        <h2 class="text-xl font-semibold text-gray-800">{{ $producto->nombre }}</h2>
                        <p class="text-gray-600 mb-2">{{ $producto->descripcion ?? 'Sin descripción' }}</p>
                        <p class="text-lg font-bold text-green-600">Q{{ number_format($producto->precio, 2) }}</p>

                        @auth
                            <form action="{{ route('carrito.agregar') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <input type="number" name="cantidad" value="1" min="1" class="w-full px-3 py-1 border border-gray-300 rounded mb-2">
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                                    Agregar al carrito
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block mt-4 text-center bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
                                Inicia sesión para comprar
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
