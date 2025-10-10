@extends('layouts.app')

@section('content')
    <style>
        .review-product-thumb {
            max-width: 88px;
            width: 100%;
            height: 88px;
            object-fit: cover;
            border-radius: 0.75rem;
            border: 1px solid rgba(229, 231, 235, 1);
        }

        .review-photo-thumb {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: 0.75rem;
            border: 1px solid rgba(229, 231, 235, 1);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.1);
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            align-items: center;
            gap: 0.35rem;
        }

        .star-rating input {
            position: absolute;
            opacity: 0;
        }

        .star-rating label {
            font-size: 1.5rem;
            color: #d1d5db;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f59e0b;
        }
    </style>

    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-star text-yellow-400"></i>
            Mis reseñas de productos
        </h1>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                <strong>Ups.</strong> Revisa la información marcada en rojo y vuelve a intentarlo.
            </div>
        @endif

        @php
            $pendientes = $itemsEntregados->filter(fn($item) => !$item->reseña);
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-10">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Productos listos para calificar</h2>
                <span class="inline-flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Entregados recientemente
                </span>
            </div>

            <div class="p-6 space-y-6">
                @if ($pendientes->isEmpty())
                    <div class="text-center text-gray-500 py-10">
                        <i class="fas fa-box-open text-4xl mb-3"></i>
                        <p>No tienes productos pendientes de reseña por ahora.</p>
                    </div>
                @else
                    @foreach ($pendientes as $item)
                        <div class="border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow transition duration-200">
                            <div class="flex flex-col md:flex-row md:items-start gap-5">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . ($item->producto->imagen ?? 'default.png')) }}"
                                         alt="{{ $item->producto->nombre }}"
                                         class="review-product-thumb">
                                </div>
                                <div class="flex-1 space-y-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $item->producto->nombre }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Pedido #{{ $item->pedido->codigo ?? $item->pedido_id }} • Entregado el {{ optional($item->updated_at)->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    <form action="{{ route('cliente.reseñas.store', $item) }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">

                                        @php
                                            $ratingSeleccionado = (int) old('item_id') === $item->id ? (int) old('estrellas', 5) : 5;
                                        @endphp
                                        <div>
                                            <span class="block text-sm font-medium text-gray-700 mb-1">Calificación</span>
                                            <div class="star-rating" role="radiogroup" aria-label="Calificación en estrellas">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    @php
                                                        $inputId = 'estrella-' . $item->id . '-' . $i;
                                                    @endphp
                                                    <input type="radio"
                                                           id="{{ $inputId }}"
                                                           name="estrellas"
                                                           value="{{ $i }}"
                                                           {{ $ratingSeleccionado === $i ? 'checked' : '' }}
                                                           class="sr-only">
                                                    <label for="{{ $inputId }}" title="{{ $i }} {{ \Illuminate\Support\Str::plural('estrella', $i) }}">
                                                        <i class="fas fa-star"></i>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>

                                        <div>
                                            <label for="comentario-{{ $item->id }}" class="text-sm font-medium text-gray-700">Comentario (opcional)</label>
                                            <textarea id="comentario-{{ $item->id }}" name="comentario" rows="3"
                                                      class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-400 focus:ring-amber-200 text-sm"
                                                      placeholder="Cuéntanos tu experiencia con el producto">{{ (int) old('item_id') === $item->id ? old('comentario') : '' }}</textarea>
                                        </div>

                                        <div>
                                            <label for="foto-{{ $item->id }}" class="text-sm font-medium text-gray-700">Foto del producto (opcional)</label>
                                            <input id="foto-{{ $item->id }}" name="foto" type="file" accept="image/*"
                                                   class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-amber-50 file:text-amber-600 hover:file:bg-amber-100">
                                            <p class="mt-1 text-xs text-gray-400">Formatos JPG o PNG, tamaño máximo 2 MB.</p>
                                            @if ($errors->has('foto') && (int) old('item_id') === $item->id)
                                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('foto') }}</p>
                                            @endif
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-sm transition">
                                                <i class="fas fa-paper-plane"></i>
                                                Enviar reseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Reseñas enviadas</h2>
                <span class="text-sm text-gray-500">Historial completo</span>
            </div>

            <div class="p-6 space-y-6">
                @if ($reseñas->isEmpty())
                    <div class="text-center text-gray-500 py-10">
                        <i class="fas fa-pen-nib text-4xl mb-3"></i>
                        <p>Aún no has dejado reseñas. ¡Cuéntanos qué te pareció tu compra!</p>
                    </div>
                @else
                    @foreach ($reseñas as $reseña)
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex flex-col md:flex-row md:items-start gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $reseña->producto->nombre ?? 'Producto eliminado' }}</h3>
                                        <span class="text-xs text-gray-500">{{ $reseña->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $reseña->estrellas ? 'fas' : 'far' }} fa-star text-yellow-400"></i>
                                        @endfor
                                        <span class="text-sm text-gray-500">Pedido #{{ $reseña->pedido->codigo ?? $reseña->pedido_id }}</span>
                                    </div>
                                    @if ($reseña->comentario)
                                        <p class="mt-3 text-gray-600 leading-relaxed">{{ $reseña->comentario }}</p>
                                    @endif
                                    @if ($reseña->imagenes && $reseña->imagenes->isNotEmpty())
                                        <div class="mt-4 flex flex-wrap gap-3">
                                            @foreach ($reseña->imagenes as $imagen)
                                                <img src="{{ asset('storage/' . $imagen->ruta) }}" alt="Foto del producto reseñado" class="review-photo-thumb">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @if ($reseña->respuesta_vendedor)
                                    <div class="md:w-64 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-900">
                                        <div class="font-semibold mb-1 flex items-center gap-2">
                                            <i class="fas fa-reply"></i>
                                            Respuesta del vendedor
                                        </div>
                                        <p>{{ $reseña->respuesta_vendedor }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
