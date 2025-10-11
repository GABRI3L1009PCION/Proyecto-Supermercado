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
        .average-stars {
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
        .average-info { text-align: right; line-height: 1.4; }

        .star-rating {
            display: inline-flex;
            align-items: center;
            gap: 0.18rem;
        }

        .star-icon {
            position: relative;
            width: 1.15rem;
            height: 1.15rem;
            display: inline-block;
        }

        .star-icon__layer {
            position: absolute;
            inset: 0;
            line-height: 1.15rem;
            font-size: 1.1rem;
            text-align: center;
        }

        .star-icon__base {
            color: rgba(209, 107, 165, 0.25);
        }

        .star-icon__fill {
            color: var(--estrella);
            overflow: hidden;
            width: var(--fill, 0%);
            display: block;
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

        .score-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .reaction-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            border: 1px solid rgba(209, 107, 165, 0.24);
            background: rgba(209, 107, 165, 0.16);
            color: var(--vino);
            font-size: 0.78rem;
            font-weight: 600;
        }

        .review-card {
            position: relative;
            border-radius: 18px;
            border: 1px solid rgba(209, 107, 165, 0.18);
            padding: 1.75rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,250,253,0.9));
            box-shadow: 0 18px 35px rgba(90, 10, 46, 0.08);
            overflow: hidden;
            animation: slideUp .45s ease both;
            animation-delay: calc(.05s * var(--delay, 0));
        }
        .review-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(209, 107, 165, 0.08), rgba(90, 10, 46, 0.05));
            opacity: 0;
            transition: opacity .25s ease;
            pointer-events: none;
        }
        .review-card:hover::before { opacity: 1; }

        .review-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1.5rem;
            position: relative;
        }
        .review-card__stars {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 1rem;
            color: var(--vino);
        }
        .review-card__stars i { color: var(--estrella); }
        .review-card__score { font-weight: 600; font-size: 0.9rem; color: var(--gris); }

        .review-card__title {
            margin: 0.35rem 0 0.2rem;
            font-size: 1.3rem;
            color: #2b1b2a;
            font-weight: 700;
        }
        .review-card__product {
            margin: 0;
            font-size: 0.9rem;
            color: var(--gris);
        }

        .review-card__meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.4rem;
            text-align: right;
        }
        .review-card__customer { font-weight: 600; color: var(--vino); }
        .review-card__date { font-size: 0.82rem; color: var(--gris); }
        .review-card__reaction {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: rgba(209, 107, 165, 0.12);
            color: var(--vino);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .review-card__comment {
            margin-top: 1rem;
            font-size: 0.98rem;
            color: #403240;
            line-height: 1.6;
        }

        .review-card__grid {
            margin-top: 1.4rem;
            display: grid;
            gap: 1.4rem;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        }
        .review-card__panel {
            background: rgba(255,255,255,0.9);
            border-radius: 14px;
            border: 1px solid rgba(209, 107, 165, 0.14);
            padding: 1.1rem 1.2rem;
            backdrop-filter: blur(2px);
        }
        .review-card__panel h4 {
            margin: 0 0 0.85rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gris);
        }
        .review-card__list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 0.65rem;
        }
        .review-card__list-item {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.9rem;
            color: #43354a;
        }
        .review-card__list-item i { font-size: 0.9rem; margin-top: 2px; }
        .review-card__list-item--positive i { color: #35b37e; }
        .review-card__list-item--alert i { color: #d94865; }
        .review-card__list-item--empty { color: var(--gris); font-style: italic; }

        .review-metrics {
            margin-top: 1.2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }
        .review-metrics span {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(90, 10, 46, 0.08);
            color: var(--vino);
        }

        .review-gallery {
            margin-top: 1.4rem;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .review-gallery a {
            display: block;
            width: 96px;
            height: 96px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .review-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .review-gallery a:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 18px 35px rgba(0,0,0,0.18);
        }

        .review-card__footer {
            margin-top: 1.4rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.82rem;
            color: var(--gris);
        }

        .reply-box {
            margin-top: 1.2rem;
            padding: 0.9rem 1.1rem;
            border-left: 4px solid var(--rosa);
            background: #faf4f7;
            border-radius: 12px;
        }
        .reply-box strong { color: var(--vino); }

        .reply-form {
            margin-top: 1.2rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .reply-form textarea {
            width: 100%;
            min-height: 80px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 0.7rem 0.8rem;
            font-size: .9rem;
            resize: vertical;
        }
        .reply-form button {
            align-self: flex-end;
            background: var(--vino);
            color: #fff;
            border: none;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
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
                <span class="sr-only">Promedio general {{ number_format($promedio ?? 0, 1) }} de 5 estrellas</span>
                <div class="average-stars star-rating" aria-hidden="true">
                    @php $valorPromedio = $promedio ?? 0; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @php
                            $fill = max(min(($valorPromedio - ($i - 1)) * 100, 100), 0);
                        @endphp
                        <span class="star-icon" style="--fill: {{ $fill }}%;">
                            <span class="star-icon__layer star-icon__base">★</span>
                            <span class="star-icon__layer star-icon__fill">★</span>
                        </span>
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
                @php
                    $agrupados = $r->aspectos_agrupados;
                    $positivos = collect($agrupados['positivos'] ?? []);
                    $alertas = collect($agrupados['alertas'] ?? []);
                    $otros = collect($agrupados['otros'] ?? []);
                    $hasScores = $r->uso_score || $r->comodidad_score || $r->duracion_score;
                @endphp
                <article class="review-card" style="--delay: {{ $loop->index }};">
                    <header class="review-card__header">
                        <div>
                            <div class="review-card__stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $r->estrellas ? 'fas fa-star' : 'far fa-star' }}"></i>
                                @endfor
                                <span class="review-card__score">{{ $r->estrellas }}/5</span>
                            </div>
                            <h3 class="review-card__title">{{ $r->resumen_titular }}</h3>
                            <p class="review-card__product">
                                {{ $r->producto->nombre ?? 'Producto eliminado' }}
                                @if($r->categoria_contexto_label)
                                    · {{ $r->categoria_contexto_label }}
                                @endif
                            </p>
                        </div>
                        <div class="review-card__meta">
                            <span class="review-card__customer"><i class="fas fa-user-circle"></i> {{ $r->cliente->name ?? 'Cliente desconocido' }}</span>
                            <span class="review-card__date">{{ $r->created_at ? $r->created_at->format('d M, Y') : 'Fecha desconocida' }}</span>
                            @if($r->reaccion_label)
                                <span class="review-card__reaction"><i class="fas fa-heart"></i> {{ $r->reaccion_label }}</span>
                            @endif
                        </div>
                    </header>

                    <p class="review-card__comment">{{ $r->comentario ?: 'El cliente no dejó comentarios adicionales.' }}</p>

                    <div class="review-card__grid">
                        <div class="review-card__panel">
                            <h4>Detalles según clientes</h4>
                            <ul class="review-card__list">
                                @php
                                    $tieneAspectos = $positivos->isNotEmpty() || $alertas->isNotEmpty() || $otros->isNotEmpty();
                                @endphp
                                @if($tieneAspectos)
                                    @foreach($positivos as $item)
                                        <li class="review-card__list-item review-card__list-item--positive">
                                            <i class="fas {{ $item['icon'] ?? 'fa-check-circle' }}"></i>
                                            <span>{{ $item['label'] }}</span>
                                        </li>
                                    @endforeach
                                    @foreach($alertas as $item)
                                        <li class="review-card__list-item review-card__list-item--alert">
                                            <i class="fas {{ $item['icon'] ?? 'fa-circle-exclamation' }}"></i>
                                            <span>{{ $item['label'] }}</span>
                                        </li>
                                    @endforeach
                                    @foreach($otros as $item)
                                        <li class="review-card__list-item">
                                            <i class="fas {{ $item['icon'] ?? 'fa-circle' }}"></i>
                                            <span>{{ $item['label'] }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="review-card__list-item review-card__list-item--empty">
                                        <i class="fas fa-circle-info"></i>
                                        <span>Sin aspectos destacados por el cliente.</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="review-card__panel">
                            <h4>Experiencia de compra</h4>
                            <ul class="review-card__list">
                                <li class="review-card__list-item">
                                    <i class="fas fa-receipt"></i>
                                    <span>Pedido {{ $r->pedido?->codigo ?? ($r->pedido_id ? '#'.$r->pedido_id : '—') }}</span>
                                </li>
                                @if($r->tiempo_uso_label)
                                    <li class="review-card__list-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $r->tiempo_uso_label }}</span>
                                    </li>
                                @endif
                                @if($r->talla_percebida_label)
                                    <li class="review-card__list-item">
                                        <i class="fas fa-ruler-horizontal"></i>
                                        <span>{{ $r->talla_percebida_label }}</span>
                                    </li>
                                @endif
                                @if($r->categoria_contexto_label)
                                    <li class="review-card__list-item">
                                        <i class="fas fa-tag"></i>
                                        <span>{{ $r->categoria_contexto_label }}</span>
                                    </li>
                                @endif
                                @if($r->tiempo_uso_label || $r->talla_percebida_label)
                                    <li class="review-card__list-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Compra verificada el {{ $r->created_at ? $r->created_at->format('d M, Y') : '—' }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </header>

                    @if($hasScores)
                        <div class="review-metrics">
                            @if($r->uso_score)
                                <span><i class="fas fa-sun"></i> Uso {{ $r->uso_score }}/5</span>
                            @endif
                            @if($r->comodidad_score)
                                <span><i class="fas fa-feather"></i> Comodidad {{ $r->comodidad_score }}/5</span>
                            @endif
                            @if($r->duracion_score)
                                <span><i class="fas fa-hourglass-half"></i> Duración {{ $r->duracion_score }}/5</span>
                            @endif
                        </div>
                    @endif

                    @if(isset($r->imagenes) && $r->imagenes->count() > 0)
                        <div class="review-gallery" role="list">
                            @foreach($r->imagenes as $img)
                                <a href="{{ asset('storage/' . $img->ruta) }}" target="_blank" rel="noopener" role="listitem">
                                    <img src="{{ asset('storage/' . $img->ruta) }}" alt="Imagen de reseña del producto {{ $r->producto->nombre ?? '' }}">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="review-card__footer">
                        <span><i class="fas fa-store"></i> {{ config('app.name', 'Supermercado Atlantia') }}</span>
                        <span><i class="fas fa-shield-check"></i> Compra verificada</span>
                    </div>

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
                </article>
            @endforeach
        @endif
    </div>
@endsection
