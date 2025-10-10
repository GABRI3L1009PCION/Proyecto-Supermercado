@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Título + volver -->
        <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-star text-yellow-400"></i>
                Mis reseñas de productos
            </h1>

            <a href="{{ route('cliente.panel') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition"
               onclick="if(!this.href){event.preventDefault();history.back();}">
                <i class="fas fa-arrow-left"></i>
                Volver al panel
            </a>
        </div>

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

        @php
            $itemsEntregados = $itemsEntregados ?? collect();
            $reseñas = $reseñas ?? collect();
            $pendientes = $itemsEntregados->filter(fn($item) => !$item->reseña);
        @endphp

            <!-- ===========================
             Productos listos para calificar
        ============================ -->
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
                        <div class="border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-lg transition duration-200 review-pending-card" style="--delay: {{ $loop->index }};">
                            <div class="flex flex-col md:flex-row md:items-start gap-5">
                                <!-- Imagen de producto -->
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . ($item->producto->imagen ?? 'default.png')) }}"
                                         alt="{{ $item->producto->nombre }}"
                                         class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl border border-gray-200">
                                </div>

                                <div class="flex-1 space-y-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $item->producto->nombre }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Pedido #{{ $item->pedido->codigo ?? $item->pedido_id }}
                                            • Entregado el {{ optional($item->updated_at)->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    <!-- Formulario de reseña -->
                                    <form action="{{ route('cliente.reseñas.store', $item) }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="space-y-4 review-form">
                                        @csrf

                                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                            <label class="text-sm font-medium text-gray-700 flex items-center">
                                                Calificación
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <select name="estrellas" required
                                                    class="w-full sm:w-auto rounded-lg border-gray-300 focus:border-amber-400 focus:ring-amber-200 text-sm">
                                                <option value="" disabled selected>Selecciona una calificación</option>
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <option value="{{ $i }}">{{ $i }} {{ \Illuminate\Support\Str::plural('estrella', $i) }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div>
                                            <label for="comentario-{{ $item->id }}" class="text-sm font-medium text-gray-700">
                                                Comentario (opcional)
                                            </label>
                                            <textarea id="comentario-{{ $item->id }}" name="comentario" rows="3"
                                                      class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-400 focus:ring-amber-200 text-sm"
                                                      placeholder="Cuéntanos tu experiencia con el producto"></textarea>
                                        </div>

                                        <!-- Subida de fotos + miniaturas -->
                                        <div>
                                            <label for="fotos-{{ $item->id }}" class="text-sm font-medium text-gray-700">
                                                Fotos del producto recibido (opcional)
                                            </label>
                                            <input id="fotos-{{ $item->id }}" name="fotos[]" type="file" accept="image/*"
                                                   multiple data-preview="#preview-{{ $item->id }}"
                                                   class="mt-1 block w-full cursor-pointer rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-700 hover:border-amber-400 focus:outline-none">
                                            <p class="mt-1 text-xs text-gray-500">
                                                Puedes seleccionar varias imágenes. Máx. 5MB por imagen (JPG, PNG, WEBP).
                                            </p>

                                            <!-- Miniaturas -->
                                            <div id="preview-{{ $item->id }}"
                                                 class="mt-3 grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2"></div>
                                        </div>

                                        <!-- Botón enviar - mismo tamaño que "Volver al panel" y con colores solicitados -->
                                        <div class="mt-4">
                                            <hr class="border-gray-100 mb-3">
                                            <div class="flex justify-end">
                                                <button type="submit"
                                                        class="submit-review-btn btn-vino inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold shadow-sm transition">
                                                    <i class="fas fa-paper-plane"></i>
                                                    <span class="btn-text">Enviar reseña</span>
                                                    <i class="fas fa-spinner fa-spin loading-icon hidden"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- ===========================
             Reseñas enviadas (historial)
        ============================ -->
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
                        <div class="border border-gray-100 rounded-xl p-5 review-history-card" style="--delay: {{ $loop->index }};">
                            <div class="flex flex-col md:flex-row md:items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <h3 class="text-lg font-semibold text-gray-800 truncate">
                                            {{ $reseña->producto->nombre ?? 'Producto eliminado' }}
                                        </h3>
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

                                    @if (isset($reseña->fotos) && $reseña->fotos->count())
                                        <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                            @foreach ($reseña->fotos as $foto)
                                                <a href="{{ asset('storage/' . $foto->ruta) }}" target="_blank" class="block group">
                                                    <img src="{{ asset('storage/' . $foto->ruta) }}" alt="Foto reseña"
                                                         class="h-20 w-full object-cover rounded-md border border-gray-200 group-hover:opacity-90 transition">
                                                </a>
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

    <!-- Script de previsualización de imágenes -->
    <script>
        document.addEventListener('change', function (e) {
            const input = e.target;
            if (!input.matches('input[type="file"][data-preview]')) return;

            const preview = document.querySelector(input.getAttribute('data-preview'));
            if (!preview) return;

            preview.innerHTML = '';
            const files = Array.from(input.files || []);
            files.slice(0, 10).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.alt = file.name;
                    img.className = 'h-16 w-16 md:h-20 md:w-20 object-cover rounded-md border border-gray-200';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }, { passive: true });

        // Feedback de envío
        document.addEventListener('DOMContentLoaded', function() {
            const reviewForms = document.querySelectorAll('.review-form');
            reviewForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn   = this.querySelector('.submit-review-btn');
                    const btnText     = submitBtn.querySelector('.btn-text');
                    const loadingIcon = submitBtn.querySelector('.loading-icon');

                    const starsSelect = this.querySelector('select[name="estrellas"]');
                    if (!starsSelect.value) {
                        e.preventDefault();
                        alert('Por favor, selecciona una calificación con estrellas.');
                        starsSelect.focus();
                        return;
                    }

                    if (submitBtn && btnText && loadingIcon) {
                        btnText.textContent = 'Enviando...';
                        loadingIcon.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                    }
                });
            });
        });
    </script>

    <style>
        /* Botón vino tinto con letras oro (mismo tamaño que "Volver al panel") */
        :root { --vino:#5a0a2e; --vino-dark:#4b0827; --oro:#d4af37; }
        .btn-vino{
            background: var(--vino);
            color: var(--oro);
            border: 1px solid var(--vino-dark);
        }
        .btn-vino:hover{ filter: brightness(1.05); }
        .btn-vino:active{ transform: translateY(1px); }
        .btn-vino i{ color: var(--oro); }

        /* Responsivo + accesibilidad */
        @media (max-width: 640px){
            .submit-review-btn{ width:100%; justify-content:center; }
        }
        .submit-review-btn:focus{ outline:2px solid var(--oro); outline-offset:2px; }

        select, textarea, input{ transition: all .2s ease-in-out; }

        .review-pending-card,
        .review-history-card{
            animation: cardFade .45s ease both;
            animation-delay: calc(.05s * var(--delay, 0));
            position: relative;
        }

        .review-pending-card:hover,
        .review-history-card:hover{
            transform: translateY(-2px);
        }

        @keyframes cardFade{
            from{opacity:0;transform:translateY(16px);}
            to{opacity:1;transform:translateY(0);}
        }
    </style>
@endsection
