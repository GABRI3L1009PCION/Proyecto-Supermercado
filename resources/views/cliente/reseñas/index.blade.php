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
                                          class="review-form">
                                        @csrf

                                        <div class="client-review-card">
                                            <div class="crc-header">
                                                <div class="crc-logo" aria-hidden="true">A</div>
                                                <div>
                                                    <span class="crc-brand">Atlantia</span>
                                                    <h3 class="crc-title">Deja tu reseña</h3>
                                                    <p class="crc-subtitle">Queremos saber cómo te fue con tu compra, sin importar si fue maravillosa o si hay algo que debamos mejorar.</p>
                                                </div>
                                            </div>

                                            <div class="crc-body">
                                                <div class="crc-field">
                                                    <label for="titulo-{{ $item->id }}" class="crc-label">Título de la reseña</label>
                                                    <input id="titulo-{{ $item->id }}" name="titulo"
                                                           type="text"
                                                           class="crc-input"
                                                           placeholder="Superó mis expectativas 😍">
                                                </div>

                                                <div class="crc-field">
                                                    <label class="crc-label">Califica tu experiencia <span class="required">*</span></label>
                                                    <div class="rating-stars" data-rating-group data-rating-messages='{"1":"Necesita mejorar","2":"Regular, podría ser mejor","3":"Buena compra","4":"Muy buena, me gustó","5":"¡Superó mis expectativas!"}'>
                                                        <input type="hidden" name="estrellas" required>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <button type="button"
                                                                    class="rating-star"
                                                                    data-value="{{ $i }}"
                                                                    aria-label="{{ $i }} {{ \Illuminate\Support\Str::plural('estrella', $i) }}">
                                                                <span class="star-icon">★</span>
                                                            </button>
                                                        @endfor
                                                        <div class="rating-hint" data-rating-hint>Selecciona una calificación</div>
                                                    </div>
                                                </div>

                                                <div class="crc-field">
                                                    <label for="contexto-{{ $item->id }}" class="crc-label">Tipo de producto</label>
                                                    <select id="contexto-{{ $item->id }}" name="categoria_contexto" class="crc-input">
                                                        <option value="">Selecciona una categoría</option>
                                                        <option value="alimentos">Alimentos y bebidas</option>
                                                        <option value="hogar">Hogar y limpieza</option>
                                                        <option value="tecnologia">Tecnología y gadgets</option>
                                                        <option value="bienestar">Cuidado personal y bienestar</option>
                                                        <option value="mascotas">Mascotas</option>
                                                        <option value="otros">Otro tipo de producto</option>
                                                    </select>
                                                </div>

                                                <div class="crc-field">
                                                    <label for="comentario-{{ $item->id }}" class="crc-label">Comentario</label>
                                                    <textarea id="comentario-{{ $item->id }}" name="comentario" rows="4"
                                                              class="crc-input crc-textarea"
                                                              placeholder="El producto llegó en excelente estado, justo como en las fotos. ¡Lo recomiendo!"></textarea>
                                                </div>

                                                <div class="crc-field">
                                                    <span class="crc-label">¿Qué destacarías?</span>
                                                    <div class="tag-grid">
                                                        <label class="tag-checkbox">
                                                            <input type="checkbox" name="aspectos[]" value="presentacion_cuidada">
                                                            <span><i class="fas fa-box"></i> Presentación cuidada</span>
                                                        </label>
                                                        <label class="tag-checkbox">
                                                            <input type="checkbox" name="aspectos[]" value="buen_sabor">
                                                            <span><i class="fas fa-utensils"></i> Sabor/calidad sorprendentes</span>
                                                        </label>
                                                        <label class="tag-checkbox">
                                                            <input type="checkbox" name="aspectos[]" value="aroma_duradero">
                                                            <span><i class="fas fa-wind"></i> Aroma duradero</span>
                                                        </label>
                                                        <label class="tag-checkbox">
                                                            <input type="checkbox" name="aspectos[]" value="entrega_rapida">
                                                            <span><i class="fas fa-truck-fast"></i> Entrega puntual</span>
                                                        </label>
                                                        <label class="tag-checkbox">
                                                            <input type="checkbox" name="aspectos[]" value="no_funciono">
                                                            <span><i class="fas fa-circle-exclamation"></i> No funcionó como esperaba</span>
                                                        </label>
                                                        <label class="tag-checkbox">
                                                            <input type="checkbox" name="aspectos[]" value="llego_danado">
                                                            <span><i class="fas fa-box-open"></i> Llegó con detalles o daños</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="crc-field">
                                                    <label for="fotos-{{ $item->id }}" class="crc-label">Foto del producto (opcional)</label>
                                                    <label for="fotos-{{ $item->id }}" class="file-upload">
                                                        <i class="fas fa-camera"></i>
                                                        <span>Subir foto</span>
                                                        <input id="fotos-{{ $item->id }}" name="fotos[]" type="file" accept="image/*" multiple data-preview="#preview-{{ $item->id }}">
                                                    </label>
                                                    <p class="crc-help">Puedes adjuntar hasta 6 imágenes de 5MB (JPG, PNG o WEBP).</p>
                                                    <div id="preview-{{ $item->id }}" class="preview-grid"></div>
                                                </div>

                                                <div class="crc-field crc-field--columns">
                                                    <div>
                                                        <label for="tiempo-{{ $item->id }}" class="crc-label">Tiempo de uso</label>
                                                        <select id="tiempo-{{ $item->id }}" name="tiempo_uso" class="crc-input">
                                                            <option value="">Selecciona una opción</option>
                                                            <option value="menos_semana">Menos de una semana</option>
                                                            <option value="dos_semanas">Alrededor de 2 semanas</option>
                                                            <option value="un_mes">1 mes</option>
                                                            <option value="tres_meses">3 meses</option>
                                                            <option value="seis_meses">6 meses</option>
                                                            <option value="mas_ano">Más de un año</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <span class="crc-label">¿Lo volverías a comprar?</span>
                                                        <div class="choice-group" data-choice-group>
                                                            <button type="button" class="choice-pill" data-value="lo_volveria_a_comprar">
                                                                <i class="fas fa-rotate-left"></i> Sí, sin dudarlo
                                                            </button>
                                                            <button type="button" class="choice-pill choice-pill--danger" data-value="necesita_mejoras">
                                                                <i class="fas fa-thumbs-down"></i> No esta vez
                                                            </button>
                                                            <input type="hidden" name="reaccion" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="crc-footer">
                                                <button type="reset" class="btn-ghost">
                                                    <i class="fas fa-undo"></i>
                                                    Cancelar
                                                </button>
                                                <button type="submit"
                                                        class="submit-review-btn btn-vino">
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
                                            <span class="history-star {{ $i <= $reseña->estrellas ? 'is-filled' : '' }}">★</span>
                                        @endfor
                                        <span class="text-sm text-gray-500">Pedido #{{ $reseña->pedido->codigo ?? $reseña->pedido_id }}</span>
                                    </div>

                                    @if ($reseña->comentario)
                                        <p class="mt-3 text-gray-600 leading-relaxed">{{ $reseña->comentario }}</p>
                                    @endif

                                    @php
                                        $hasScores = $reseña->uso_score || $reseña->comodidad_score || $reseña->duracion_score;
                                    @endphp

                                    @if ($hasScores)
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold text-rose-600">
                                            @if ($reseña->uso_score)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-rose-600"><i class="fas fa-sun"></i> Uso {{ $reseña->uso_score }}/5</span>
                                            @endif
                                            @if ($reseña->comodidad_score)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-rose-600"><i class="fas fa-feather"></i> Comodidad {{ $reseña->comodidad_score }}/5</span>
                                            @endif
                                            @if ($reseña->duracion_score)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-rose-600"><i class="fas fa-hourglass-half"></i> Duración {{ $reseña->duracion_score }}/5</span>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($reseña->talla_percebida_label)
                                        <div class="mt-2 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600">
                                            <i class="fas fa-ruler-horizontal"></i> {{ $reseña->talla_percebida_label }}
                                        </div>
                                    @endif

                                    @if ($reseña->reaccion_label)
                                        <div class="mt-2 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600">
                                            <i class="fas fa-heart"></i> {{ $reseña->reaccion_label }}
                                        </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            const reviewForms = document.querySelectorAll('.review-form');

            reviewForms.forEach(form => {
                const ratingGroup = form.querySelector('[data-rating-group]');

                if (ratingGroup) {
                    const ratingInput = ratingGroup.querySelector('input[name="estrellas"]');
                    const ratingHint  = ratingGroup.querySelector('[data-rating-hint]');
                    const starButtons = Array.from(ratingGroup.querySelectorAll('.rating-star'));
                    let previewValue = 0;

                    const getStoredValue = () => {
                        if (!ratingInput) {
                            return 0;
                        }
                        const numericValue = Number(ratingInput.value);
                        return Number.isNaN(numericValue) ? 0 : numericValue;
                    };

                    let ratingMessages = {};
                    try {
                        ratingMessages = JSON.parse(ratingGroup.dataset.ratingMessages || '{}');
                    } catch (error) {
                        ratingMessages = {};
                    }

                    const updateMessage = value => {
                        if (!ratingHint) return;
                        const numericValue = Number(value);
                        if (!numericValue) {
                            ratingHint.textContent = 'Selecciona una calificación';
                            return;
                        }
                        ratingHint.textContent = ratingMessages[numericValue] || `Calificación: ${numericValue} de 5`;
                    };

                    const paintStars = value => {
                        starButtons.forEach(button => {
                            const starValue = Number(button.dataset.value);
                            const isActive = starValue <= value;
                            button.classList.toggle('is-active', isActive);
                            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                        });
                    };

                    const commitValue = value => {
                        if (!ratingInput) return;
                        if (Number(ratingInput.value) === value) {
                            ratingInput.value = '';
                            paintStars(0);
                            updateMessage(0);
                            return;
                        }

                        ratingInput.value = value;
                        paintStars(value);
                        updateMessage(value);
                    };

                    starButtons.forEach(button => {
                        const value = Number(button.dataset.value);

                        button.addEventListener('click', event => {
                            event.preventDefault();
                            commitValue(value);
                        });

                        button.addEventListener('mouseenter', () => {
                            previewValue = value;
                            paintStars(value);
                            updateMessage(value);
                        });

                        button.addEventListener('focus', () => {
                            previewValue = value;
                            paintStars(value);
                            updateMessage(value);
                        });

                        button.addEventListener('keydown', event => {
                            if (event.key === 'ArrowRight' || event.key === 'ArrowUp') {
                                event.preventDefault();
                                const nextValue = Math.min(value + 1, 5);
                                const nextButton = starButtons.find(btn => Number(btn.dataset.value) === nextValue);
                                if (nextButton) {
                                    nextButton.focus();
                                    commitValue(nextValue);
                                }
                            }

                            if (event.key === 'ArrowLeft' || event.key === 'ArrowDown') {
                                event.preventDefault();
                                const previousValue = Math.max(value - 1, 1);
                                const previousButton = starButtons.find(btn => Number(btn.dataset.value) === previousValue);
                                if (previousButton) {
                                    previousButton.focus();
                                    commitValue(previousValue);
                                }
                            }

                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                commitValue(value);
                            }
                        });

                        button.addEventListener('mouseleave', () => {
                            previewValue = 0;
                            const storedValue = getStoredValue();
                            paintStars(storedValue);
                            updateMessage(storedValue);
                        });

                        button.addEventListener('blur', () => {
                            if (previewValue) {
                                previewValue = 0;
                                const storedValue = getStoredValue();
                                paintStars(storedValue);
                                updateMessage(storedValue);
                            }
                        });
                    });

                    ratingGroup.addEventListener('mouseleave', () => {
                        const storedValue = getStoredValue();
                        paintStars(storedValue);
                        updateMessage(storedValue);
                    });
                }

                const choiceGroups = form.querySelectorAll('[data-choice-group]');
                choiceGroups.forEach(group => {
                    const hiddenInput = group.querySelector('input[type="hidden"][name="reaccion"]');
                    const pills = group.querySelectorAll('.choice-pill');

                    pills.forEach(pill => {
                        pill.addEventListener('click', event => {
                            event.preventDefault();
                            if (!hiddenInput) return;

                            const value = pill.dataset.value || '';
                            const isSame = hiddenInput.value === value;

                            pills.forEach(btn => btn.classList.remove('is-active'));

                            if (isSame) {
                                hiddenInput.value = '';
                                return;
                            }

                            hiddenInput.value = value;
                            pill.classList.add('is-active');
                        });
                    });
                });

                form.addEventListener('submit', function(e) {
                    const submitBtn   = this.querySelector('.submit-review-btn');
                    const btnText     = submitBtn ? submitBtn.querySelector('.btn-text') : null;
                    const loadingIcon = submitBtn ? submitBtn.querySelector('.loading-icon') : null;
                    const starsInput  = this.querySelector('input[name="estrellas"]');

                    if (!starsInput || !starsInput.value) {
                        e.preventDefault();
                        alert('Por favor, selecciona una calificación con estrellas.');
                        const firstStar = this.querySelector('.rating-star');
                        if (firstStar) {
                            firstStar.focus();
                        }
                        return;
                    }

                    if (submitBtn && btnText && loadingIcon) {
                        btnText.textContent = 'Enviando...';
                        loadingIcon.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                    }
                });

                form.addEventListener('reset', () => {
                    setTimeout(() => {
                        const starsInput = form.querySelector('input[name="estrellas"]');
                        if (starsInput) {
                            starsInput.value = '';
                        }

                        const hint = form.querySelector('[data-rating-hint]');
                        if (hint) {
                            hint.textContent = 'Selecciona una calificación';
                        }

                        form.querySelectorAll('.rating-star').forEach(star => {
                            star.classList.remove('is-active');
                            star.setAttribute('aria-pressed', 'false');
                        });

                        form.querySelectorAll('[data-choice-group]').forEach(group => {
                            const hiddenInput = group.querySelector('input[type="hidden"][name="reaccion"]');
                            if (hiddenInput) {
                                hiddenInput.value = '';
                            }
                            group.querySelectorAll('.choice-pill').forEach(pill => pill.classList.remove('is-active'));
                        });
                    }, 0);
                });
            });
        });
    </script>

    <style>
        /* Botón vino tinto con letras oro */
        :root {
            --vino: #5a0a2e;
            --vino-dark: #4b0827;
            --oro: #d4af37;
            --star-yellow: #fbbf24;
            --star-yellow-bright: #f59e0b;
        }

        .btn-vino{
            background: var(--vino);
            color: var(--oro);
            border: 1px solid var(--vino-dark);
        }
        .btn-vino:hover{ filter: brightness(1.05); }
        .btn-vino:active{ transform: translateY(1px); }
        .btn-vino i{ color: var(--oro); }

        .btn-ghost{
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border-radius: .75rem;
            border: 1px solid #e5cdb6;
            background: rgba(255,255,255,0.6);
            color: #8a5a2e;
            font-weight: 600;
            padding: .65rem 1.2rem;
            transition: all .2s ease;
        }
        .btn-ghost:hover{ background: rgba(255,255,255,0.85); }
        .btn-ghost:focus{ outline:2px solid var(--oro); outline-offset:2px; }

        .client-review-card{
            background: linear-gradient(140deg, #fef8f2 0%, #f5e8da 100%);
            border-radius: 1.5rem;
            border: 1px solid #f1d9c5;
            box-shadow: 0 18px 30px -24px rgba(90,10,46,.55);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .crc-header{
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .crc-logo{
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: var(--vino);
            color: var(--oro);
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: 0 10px 18px rgba(90,10,46,.25);
        }
        .crc-brand{
            text-transform: uppercase;
            letter-spacing: .24em;
            font-size: .7rem;
            color: #b9895f;
            font-weight: 700;
        }
        .crc-title{
            font-size: 1.45rem;
            font-weight: 800;
            color: #5a4228;
            margin-top: .2rem;
        }
        .crc-subtitle{
            color: #8c7358;
            font-size: .9rem;
            margin-top: .25rem;
            line-height: 1.4;
        }

        .crc-body{
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .crc-field{
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }
        .crc-field--columns{
            gap: 1rem;
        }
        .crc-field--columns > div{
            flex: 1 1 0;
        }

        .crc-label{
            font-weight: 700;
            color: #6d4b2f;
            font-size: .9rem;
        }
        .required{ color: #d24c4c; font-weight: 700; }

        .crc-input{
            width: 100%;
            border-radius: 1rem;
            border: 1px solid #efd6c1;
            background: rgba(255,255,255,0.92);
            padding: .8rem 1rem;
            font-size: .95rem;
            color: #5a4228;
            transition: all .2s ease;
            box-shadow: inset 0 1px 2px rgba(90,10,46,.08);
        }
        .crc-input:focus{
            outline: 2px solid rgba(210,119,63,.35);
            border-color: #e8b892;
            background: #fff;
        }
        .crc-textarea{
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
        }

        /* ===== ESTRELLAS MEJORADAS ===== */
        .rating-stars{
            display: flex;
            align-items: center;
            gap: .65rem;
            flex-wrap: wrap;
        }
        .rating-star{
            width: 50px;
            height: 50px;
            border-radius: 16px;
            border: 2px solid #e5cdb6;
            background: rgba(255,255,255,0.7);
            display: grid;
            place-items: center;
            font-size: 1.8rem;
            transition: all .25s ease;
            cursor: pointer;
            position: relative;
        }

        .star-icon{
            color: #d4c5a9;
            transition: all .25s ease;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .rating-star:hover,
        .rating-star:focus{
            transform: translateY(-3px) scale(1.05);
            border-color: var(--star-yellow);
            outline: none;
            box-shadow: 0 8px 16px rgba(251,191,36,.3);
        }

        .rating-star:hover .star-icon,
        .rating-star:focus .star-icon{
            color: var(--star-yellow);
        }

        .rating-star.is-active{
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: var(--star-yellow-bright);
            box-shadow: 0 6px 20px rgba(251,191,36,.4);
        }

        .rating-star.is-active .star-icon{
            color: var(--star-yellow-bright);
            text-shadow: 0 2px 8px rgba(245,158,11,.4);
        }

        .rating-hint{
            font-size: .9rem;
            color: #8c7358;
            font-weight: 600;
            margin-left: .5rem;
        }

        /* Estrellas en historial */
        .history-star{
            font-size: 1.3rem;
            color: #d4c5a9;
            transition: color .2s ease;
        }

        .history-star.is-filled{
            color: var(--star-yellow-bright);
            text-shadow: 0 2px 4px rgba(245,158,11,.3);
        }

        .tag-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: .75rem;
        }
        .tag-checkbox{
            position: relative;
            display: block;
        }
        .tag-checkbox input{
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }
        .tag-checkbox span{
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border-radius: 1rem;
            border: 1px solid #efd6c1;
            background: rgba(255,255,255,0.75);
            padding: .65rem .9rem;
            font-size: .85rem;
            color: #6d4b2f;
            transition: all .2s ease;
        }
        .tag-checkbox i{ color: #d29f68; }
        .tag-checkbox input:checked + span{
            background: rgba(210,119,63,.12);
            border-color: rgba(210,119,63,.6);
            color: #5a0a2e;
            box-shadow: 0 8px 16px rgba(210,119,63,.22);
        }

        .file-upload{
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            border: 2px dashed #e7cbb2;
            border-radius: 1.2rem;
            background: rgba(255,255,255,0.6);
            color: #7a5c3d;
            font-weight: 600;
            padding: .85rem 1.2rem;
            cursor: pointer;
            transition: all .2s ease;
            max-width: 100%;
        }
        .file-upload:hover{
            background: rgba(255,255,255,0.85);
            border-color: #d8b99a;
        }
        .file-upload i{ color: #d29f68; font-size: 1.1rem; }
        .file-upload input{
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .crc-help{
            font-size: .8rem;
            color: #9b8066;
        }
        .preview-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
            gap: .5rem;
            margin-top: .75rem;
        }
        .preview-grid img{
            width: 100%;
            height: 64px;
            object-fit: cover;
            border-radius: .75rem;
            border: 1px solid #efd6c1;
            box-shadow: 0 6px 12px rgba(90,10,46,.12);
        }

        .choice-group{
            display: inline-flex;
            flex-wrap: wrap;
            gap: .75rem;
        }
        .choice-pill{
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border-radius: 9999px;
            background: rgba(255,255,255,0.7);
            border: 1px solid #e5cdb6;
            padding: .65rem 1rem;
            color: #5a4228;
            font-weight: 600;
            transition: all .2s ease;
        }
        .choice-pill i{ color: #d29f68; }
        .choice-pill:hover,
        .choice-pill:focus{ outline:none; background: rgba(255,255,255,0.9); transform: translateY(-1px); }
        .choice-pill.is-active{
            background: #ffe8c8;
            border-color: #f2ba7d;
            color: #5a0a2e;
            box-shadow: 0 10px 18px rgba(210,119,63,.25);
        }
        .choice-pill--danger.is-active{
            background: #ffe0e0;
            border-color: #f18d8d;
            color: #c23a3a;
        }

        .crc-footer{
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            align-items: center;
            border-top: 1px solid rgba(233,198,166,.6);
            padding-top: 1.2rem;
        }
        .submit-review-btn{
            display: inline-flex;
            align-items: center;
            gap: .65rem;
            border-radius: .9rem;
            padding: .75rem 1.4rem;
            font-weight: 700;
            transition: all .2s ease;
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

        @media (max-width: 768px){
            .crc-field--columns{
                flex-direction: column;
            }
            .crc-footer{
                flex-direction: column-reverse;
                align-items: stretch;
            }
            .btn-ghost,
            .submit-review-btn{ width: 100%; justify-content: center; }

            .rating-star{
                width: 45px;
                height: 45px;
                font-size: 1.6rem;
            }
        }

        @media (max-width: 640px){
            .client-review-card{ padding: 1.4rem; }
            .rating-star{
                width: 42px;
                height: 42px;
                font-size: 1.5rem;
            }
            .tag-grid{ grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
        }

        @keyframes cardFade{
            from{opacity:0;transform:translateY(16px);}
            to{opacity:1;transform:translateY(0);}
        }
    </style>
@endsection
