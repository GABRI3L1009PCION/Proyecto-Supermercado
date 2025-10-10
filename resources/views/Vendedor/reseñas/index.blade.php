@extends('layouts.app')

@section('title', 'Reseñas de tus productos | Panel del Vendedor')

@section('content')
    <style>
        :root {
            --vino: #5a0a2e;
            --rosa: #d16ba5;
            --gris: #6c757d;
            --fondo: #f6eef2;
            --estrella: #f7b733;
            --blanco: #ffffff;
        }

        body {
            background: var(--fondo);
            font-family: 'Poppins', sans-serif;
        }

        .reviews-wrap {
            max-width: 1100px;
            margin: 2rem auto;
            background: var(--blanco);
            border-radius: 14px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.08);
            padding: 2rem;
            animation: fadeIn .45s ease;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .reviews-header h2 {
            color: var(--vino);
            font-weight: 700;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reviews-header a {
            background: var(--vino);
            color: var(--blanco);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s ease-in-out;
        }
        .reviews-header a:hover { background: var(--rosa); }

        .average-box {
            background: linear-gradient(120deg, var(--vino), var(--rosa));
            color: var(--blanco);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
        }

        .average-score { font-size: 3rem; font-weight: bold; }
        .average-stars i { color: var(--estrella); font-size: 1.3rem; margin-right: 2px; }
        .average-info { text-align: right; line-height: 1.4; }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .insight-card {
            background: #fff7fb;
            border: 1px solid rgba(209, 107, 165, 0.3);
            border-radius: 14px;
            padding: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            box-shadow: 0 10px 30px rgba(209, 107, 165, 0.12);
        }

        .insight-card span { font-size: 0.78rem; text-transform: uppercase; color: var(--gris); letter-spacing: .08em; }
        .insight-card strong { font-size: 1.4rem; color: var(--vino); }
        .insight-card small { font-size: 0.75rem; color: var(--gris); }

        .talla-bars {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            margin-bottom: 2rem;
        }

        .talla-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .talla-label {
            min-width: 110px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--vino);
        }

        .talla-progress {
            flex: 1;
            height: 10px;
            border-radius: 999px;
            background: #f3d5e3;
            overflow: hidden;
            position: relative;
        }

        .talla-progress span {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--rosa), var(--vino));
            transform-origin: left;
        }

        .talla-value {
            font-size: 0.8rem;
            color: var(--gris);
            font-weight: 600;
        }

        .review-card {
            border-bottom: 1px solid #eee;
            padding: 1.2rem 0;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            animation: slideUp .45s ease both;
            animation-delay: calc(.05s * var(--delay, 0));
            z-index: 0;
        }
        .review-card:last-child { border-bottom: none; }
        .review-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            pointer-events: none;
            opacity: 0;
            box-shadow: 0 20px 45px rgba(90, 10, 46, 0.08);
            transition: opacity .3s ease;
            z-index: -1;
        }
        .review-card:hover::after {
            opacity: 1;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: .4rem;
        }

        .review-customer { font-weight: bold; color: var(--vino); }
        .review-product { font-size: .9rem; color: var(--gris); }

        .review-stars i {
            color: var(--estrella);
            font-size: 1rem;
        }

        .review-text { color: #333; margin-top: .5rem; font-size: .95rem; line-height: 1.5; }
        .review-date { color: var(--gris); font-size: .8rem; margin-top: .3rem; }

        .score-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.6rem;
        }

        .score-chip {
            background: rgba(90, 10, 46, 0.08);
            color: var(--vino);
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .score-chip i { color: var(--estrella); }

        .reaction-badge {
            margin-top: 0.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, rgba(209, 107, 165, 0.18), rgba(90, 10, 46, 0.18));
            color: var(--vino);
            border: 1px solid rgba(209, 107, 165, 0.35);
            border-radius: 999px;
            padding: 0.4rem 0.9rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .fit-pill {
            margin-top: 0.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: rgba(90, 10, 46, 0.08);
            color: var(--vino);
            font-weight: 600;
        }

        /* 🖼️ Galería de imágenes */
        .review-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .review-gallery a {
            display: block;
            width: 100px;
            height: 100px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(0,0,0,0.12);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .review-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .review-gallery a:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 16px 32px rgba(0,0,0,0.16);
        }

        /* 💬 Respuesta del vendedor */
        .reply-box {
            margin-top: 1rem;
            padding: 0.8rem 1rem;
            border-left: 4px solid var(--rosa);
            background: #faf4f7;
            border-radius: 8px;
        }
        .reply-box strong { color: var(--vino); }

        .reply-form {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .reply-form textarea {
            width: 100%;
            min-height: 70px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 0.6rem;
            font-size: .9rem;
            resize: vertical;
        }
        .reply-form button {
            align-self: flex-end;
            background: var(--vino);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .reply-form button:hover { background: var(--rosa); }

        .empty-state { text-align: center; color: var(--gris); padding: 1rem; }

        .alert-success {
            background: #3ccf91;
            color: #fff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            animation: fadeOut 5s ease forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 992px) {
            .reviews-wrap { padding: 1.5rem; margin: 1.5rem auto; }
            .average-box { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .average-info { text-align: left; }
            .insights-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
        }

        @media (max-width: 640px) {
            .reviews-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .reviews-header a { width: 100%; text-align: center; }
            .review-header { flex-direction: column; align-items: flex-start; gap: .75rem; }
            .review-gallery { gap: 8px; }
            .review-gallery a { width: 80px; height: 80px; }
        }
    </style>

    <div class="reviews-wrap">

        {{-- ✅ Notificación de éxito --}}
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- 🔹 Encabezado --}}
        <div class="reviews-header">
            <h2><i class="fas fa-star"></i> Reseñas de tus productos</h2>
            <a href="{{ route('vendedor.dashboard') }}"><i class="fas fa-arrow-left"></i> Volver al panel</a>
        </div>

        {{-- 🔹 Promedio general --}}
        <div class="average-box">
            <div class="average-score">{{ number_format($promedio ?? 0, 1) }}</div>
            <div class="average-info">
                <div class="average-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @php $valor = $promedio ?? 0; @endphp
                        <i class="{{ $i <= floor($valor) ? 'fas fa-star' : ($i - $valor < 1 && $valor > 0 ? 'fas fa-star-half-alt' : 'far fa-star') }}"></i>
                    @endfor
                </div>
                <span>Basado en {{ $totalReseñas ?? 0 }} reseñas verificadas</span>
            </div>
        </div>

        {{-- 🔹 Métricas de experiencia --}}
        @php
            $totalTallas = $tallaDistribucion->sum();
            $topReactionKey = $reaccionesResumen->keys()->first();
            $topReactionLabel = $topReactionKey ? (\App\Models\Reseña::REACCIONES[$topReactionKey] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $topReactionKey))) : null;
        @endphp

        <div class="insights-grid">
            <div class="insight-card">
                <span>Uso diario</span>
                <strong>{{ !is_null($promediosCategoria['uso']) ? number_format($promediosCategoria['uso'], 2) : '—' }}</strong>
                <small>Promedio de usabilidad</small>
            </div>
            <div class="insight-card">
                <span>Comodidad</span>
                <strong>{{ !is_null($promediosCategoria['comodidad']) ? number_format($promediosCategoria['comodidad'], 2) : '—' }}</strong>
                <small>Percepción de confort</small>
            </div>
            <div class="insight-card">
                <span>Duración</span>
                <strong>{{ !is_null($promediosCategoria['duracion']) ? number_format($promediosCategoria['duracion'], 2) : '—' }}</strong>
                <small>Resistencia en el tiempo</small>
            </div>
            <div class="insight-card">
                <span>Reacciones destacadas</span>
                <strong>{{ $reaccionesResumen->sum() }}</strong>
                <small>{{ $topReactionLabel ?? 'Sin reacciones aún' }}</small>
            </div>
        </div>

        @if($totalTallas > 0)
            <div class="talla-bars" aria-label="Distribución de tallas percibidas">
                @foreach($tallaDistribucion as $clave => $cantidad)
                    @php
                        $porcentaje = $totalTallas ? round(($cantidad / $totalTallas) * 100) : 0;
                        $label = match($clave) {
                            'pequena' => 'Más pequeña',
                            'exacta' => 'Talla exacta',
                            'grande' => 'Más grande',
                            default => ucfirst($clave),
                        };
                    @endphp
                    <div class="talla-row">
                        <span class="talla-label">{{ $label }}</span>
                        <div class="talla-progress">
                            <span style="transform: scaleX({{ $porcentaje / 100 }});"></span>
                        </div>
                        <span class="talla-value">{{ $porcentaje }}%</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if($reaccionesResumen->isNotEmpty())
            <div class="score-chips" aria-label="Reacciones favoritas">
                @foreach($reaccionesResumen as $clave => $conteo)
                    <span class="reaction-badge"><i class="fas fa-heart"></i> {{ \App\Models\Reseña::REACCIONES[$clave] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $clave)) }} · {{ $conteo }}</span>
                @endforeach
            </div>
        @endif

        {{-- 🔹 Listado de reseñas --}}
        @if($reseñas->isEmpty())
            <p class="empty-state">Aún no tienes reseñas.</p>
        @else
            @foreach($reseñas as $r)
                <div class="review-card" style="--delay: {{ $loop->index }};">
                    <div class="review-header">
                        <div>
                            <div class="review-customer">
                                <i class="fas fa-user-circle"></i> {{ $r->cliente->name ?? 'Cliente desconocido' }}
                            </div>
                            <div class="review-product">
                                Producto: {{ $r->producto->nombre ?? 'Producto eliminado' }}
                            </div>
                            <div class="review-product">
                                Pedido: {{ $r->pedido?->codigo ?? ($r->pedido_id ? '#'.$r->pedido_id : '—') }}
                            </div>
                        </div>
                        <div class="review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $r->estrellas ? 'fas fa-star' : 'far fa-star' }}"></i>
                            @endfor
                        </div>
                    </div>

                    <p class="review-text">{{ $r->comentario ?: 'El cliente no dejó comentarios adicionales.' }}</p>

                    @php
                        $hasScores = $r->uso_score || $r->comodidad_score || $r->duracion_score;
                    @endphp

                    @if($hasScores)
                        <div class="score-chips">
                            @if($r->uso_score)
                                <span class="score-chip"><i class="fas fa-sun"></i> Uso {{ $r->uso_score }}/5</span>
                            @endif
                            @if($r->comodidad_score)
                                <span class="score-chip"><i class="fas fa-feather"></i> Comodidad {{ $r->comodidad_score }}/5</span>
                            @endif
                            @if($r->duracion_score)
                                <span class="score-chip"><i class="fas fa-hourglass-half"></i> Duración {{ $r->duracion_score }}/5</span>
                            @endif
                        </div>
                    @endif

                    @if($r->talla_percebida_label)
                        <div class="fit-pill"><i class="fas fa-ruler-horizontal"></i> {{ $r->talla_percebida_label }}</div>
                    @endif

                    @if($r->reaccion_label)
                        <button type="button" class="reaction-badge" aria-label="Reacción del cliente">
                            <i class="fas fa-heart"></i> {{ $r->reaccion_label }}
                        </button>
                    @endif

                    {{-- 🖼️ Galería --}}
                    @if(isset($r->imagenes) && $r->imagenes->count() > 0)
                        <div class="review-gallery" role="list">
                            @foreach($r->imagenes as $img)
                                <a href="{{ asset('storage/' . $img->ruta) }}" target="_blank" rel="noopener" role="listitem">
                                    <img src="{{ asset('storage/' . $img->ruta) }}" alt="Imagen de reseña del producto {{ $r->producto->nombre ?? '' }}">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="review-date">
                        <i class="fas fa-check-circle"></i> Compra verificada ·
                        {{ $r->created_at ? $r->created_at->format('d M, Y') : 'Fecha desconocida' }}
                    </div>

                    {{-- 💬 Respuesta del vendedor --}}
                    @if($r->respuesta_vendedor)
                        <div class="reply-box">
                            <strong>Tu respuesta:</strong> {{ $r->respuesta_vendedor }}
                        </div>
                    @elseif(isset($r->id))
                        <form action="{{ route('vendedor.reseñas.responder', $r->id) }}" method="POST" class="reply-form">
                            @csrf
                            <textarea name="respuesta_vendedor" placeholder="Escribe una respuesta pública al cliente..." required></textarea>
                            <button type="submit"><i class="fas fa-paper-plane"></i> Responder</button>
                        </form>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
@endsection
