@extends('layouts.app')

@section('title', 'Reseñas de tus productos | Panel del Vendedor')

@section('content')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-pY1dQ1hNUZo+sIAZ67/lbbC0xLqzM0dJkTLhALRz0BGmqeuVJQw+/7wjSc8CWfiwZBSkNjBa57a70e6m1dc+4g=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --vino: #4A052A;
            --vino-oscuro: #2e031b;
            --rosa: #C25B9A;
            --rosa-claro: #f3c4dd;
            --gris-texto: #5c6268;
            --blanco: #ffffff;
            --estrella: #fbbf24;
            --estrella-oscura: #f59e0b;
            --gris-claro: #E6E0E3;
            --sombra-card: 0 8px 30px rgba(74, 5, 42, 0.1);
        }

        body {
            background: linear-gradient(150deg, var(--vino-oscuro) 10%, #5b1035 60%, var(--rosa) 130%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            color: var(--gris-texto);
        }

        .reviews-wrap {
            max-width: 1100px;
            margin: 3rem auto;
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            box-shadow: var(--sombra-card);
            padding: 2.5rem;
            animation: slideIn .6s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
            border-bottom: 2px solid var(--rosa);
            padding-bottom: 1rem;
        }
        .reviews-header h2 {
            color: var(--vino);
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .reviews-header a {
            background: linear-gradient(135deg, var(--vino), var(--rosa));
            color: #fff;
            padding: 0.7rem 1.4rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: .3s;
        }
        .reviews-header a:hover {
            background: linear-gradient(135deg, var(--rosa), var(--vino));
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(194,91,154,0.4);
        }

        .alert-success {
            background: var(--rosa);
            color: #fff;
            border-radius: 12px;
            padding: 1rem 1.3rem;
            margin-bottom: 2rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: .7rem;
            box-shadow: var(--sombra-card);
            animation: fadeIn .5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .average-box {
            background: linear-gradient(120deg, var(--vino), var(--rosa));
            color: #fff;
            border-radius: 18px;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            margin-bottom: 2.5rem;
        }
        .average-score {
            font-size: 4rem;
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .average-info { text-align: right; }
        .average-stars {
            margin: .5rem 0;
            font-size: 1.5rem;
            display: flex;
            gap: 4px;
            justify-content: flex-end;
        }

        /* Estrellas mejoradas en promedio */
        .average-stars .star {
            color: var(--estrella);
            text-shadow: 0 2px 8px rgba(251, 191, 36, 0.5);
            font-size: 1.6rem;
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(230px,1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }
        .insight-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--gris-claro);
            box-shadow: var(--sombra-card);
            transition: all .3s;
            text-align: center;
        }
        .insight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(74,5,42,0.15);
        }
        .insight-card span {
            display: block;
            color: var(--gris-texto);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .insight-card strong {
            color: var(--vino);
            font-size: 2rem;
            display: block;
            margin-bottom: 0.3rem;
        }
        .insight-card small {
            color: var(--rosa);
            font-size: 0.8rem;
        }

        .talla-bars {
            margin-bottom: 2rem;
            background: #fff;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--sombra-card);
        }
        .talla-bars h3 {
            color: var(--vino);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .talla-row {
            display:flex;
            align-items:center;
            gap:1rem;
            margin-bottom:.8rem;
        }
        .talla-label {
            min-width:130px;
            color:var(--vino);
            font-weight:600;
            font-size: 0.9rem;
        }
        .talla-progress {
            flex:1;
            background:#f0e6eb;
            height:14px;
            border-radius:999px;
            overflow:hidden;
        }
        .talla-progress span {
            display:block;
            height:100%;
            background:linear-gradient(90deg,var(--rosa),var(--vino));
            transition:width .5s ease;
        }
        .talla-percentage {
            min-width: 50px;
            text-align: right;
            font-weight: 600;
            color: var(--vino);
        }

        .review-card {
            background: #fff;
            border: 1px solid var(--gris-claro);
            border-radius: 18px;
            padding: 2rem;
            box-shadow: var(--sombra-card);
            margin-bottom: 1.8rem;
            transition: all .3s ease;
        }
        .review-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(74,5,42,0.15);
        }

        .review-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.2rem;
            gap: 1rem;
        }

        /* ESTRELLAS MEJORADAS EN RESEÑAS */
        .review-card__stars {
            display: flex;
            gap: 4px;
            margin-bottom: 0.8rem;
        }
        .review-card__stars .star {
            font-size: 1.4rem;
            color: var(--estrella);
            text-shadow: 0 2px 4px rgba(251, 191, 36, 0.3);
        }
        .review-card__stars .star-half {
            color: var(--estrella);
        }
        .review-card__stars .star-empty {
            color: #d4c5a9;
        }

        .review-card__title {
            color: var(--vino);
            font-weight:700;
            font-size:1.3rem;
            margin-bottom: 0.3rem;
        }
        .review-card__product {
            color: var(--rosa);
            font-size: 0.95rem;
            font-weight: 500;
        }
        .review-card__meta {
            text-align:right;
            color: var(--gris-texto);
            font-size: 0.9rem;
        }
        .review-card__meta i {
            color: var(--rosa);
        }
        .review-card__comment {
            margin-top:1rem;
            line-height:1.7;
            color:#333;
            font-size: 0.95rem;
        }

        .review-card__panel {
            background: linear-gradient(135deg, #f7f3f5 0%, #fef8f2 100%);
            border-radius: 12px;
            padding: 1.2rem;
            margin-top: 1.2rem;
            border: 1px solid #f1d9c5;
        }
        .review-card__panel h4 {
            text-transform: uppercase;
            font-size: .85rem;
            color: var(--vino);
            font-weight: 700;
            border-bottom: 2px solid var(--rosa-claro);
            padding-bottom: .6rem;
            margin-bottom: .8rem;
        }
        .review-card__panel ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .review-card__panel li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 0;
            color: var(--gris-texto);
        }
        .review-card__panel li i {
            font-size: 1rem;
        }

        .review-metrics {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }
        .review-metrics span {
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            background: rgba(74,5,42,0.08);
            color: var(--vino);
            border: 1px solid rgba(74,5,42,0.15);
            border-radius:999px;
            padding:.5rem 1rem;
            font-size:.85rem;
            font-weight:600;
        }

        .review-gallery {
            margin-top:1.2rem;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }
        .review-gallery a {
            display: block;
            transition: transform .3s ease;
        }
        .review-gallery a:hover {
            transform: scale(1.05);
        }
        .review-gallery img {
            width:100px;
            height:100px;
            object-fit:cover;
            border-radius:12px;
            border:2px solid var(--gris-claro);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .reply-box {
            margin-top:1.2rem;
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            border-left:4px solid var(--rosa);
            padding:1.2rem;
            border-radius:12px;
        }
        .reply-box strong {
            color: var(--vino);
            display: block;
            margin-bottom: 0.6rem;
            font-size: 0.95rem;
        }
        .reply-box p {
            color: var(--gris-texto);
            line-height: 1.6;
        }

        .reply-form {
            margin-top: 1.2rem;
            background: #f9fafb;
            padding: 1.2rem;
            border-radius: 12px;
            border: 1px solid var(--gris-claro);
        }
        .reply-form textarea {
            width:100%;
            min-height:100px;
            padding:1rem;
            border:1px solid #ccc;
            border-radius:10px;
            resize:vertical;
            font-family:'Poppins',sans-serif;
            font-size:.95rem;
            transition: all .3s ease;
        }
        .reply-form textarea:focus {
            outline: none;
            border-color: var(--rosa);
            box-shadow: 0 0 0 3px rgba(194,91,154,0.1);
        }
        .reply-form button {
            margin-top:.8rem;
            background:var(--vino);
            color:#fff;
            border:none;
            border-radius:10px;
            padding:.7rem 1.5rem;
            font-weight: 600;
            transition:.3s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .reply-form button:hover {
            background:var(--rosa);
            transform:translateY(-2px);
            box-shadow: 0 4px 12px rgba(194,91,154,0.3);
        }

        .empty-state {
            text-align:center;
            color:var(--gris-texto);
            border:2px dashed var(--gris-claro);
            padding:3rem 2rem;
            border-radius:16px;
            background:#fff9fb;
        }
        .empty-state i {
            font-size: 3rem;
            color: var(--rosa);
            margin-bottom: 1rem;
        }
        .empty-state p {
            font-size: 1.1rem;
        }

        @media(max-width:768px){
            .average-box{
                flex-direction:column;
                align-items:flex-start;
                gap:1rem;
            }
            .average-info {
                text-align: left;
            }
            .average-stars {
                justify-content: flex-start;
            }
            .reviews-header{
                flex-direction:column;
                gap:1rem;
                align-items:flex-start;
            }
            .review-card__header {
                flex-direction: column;
            }
            .review-card__meta {
                text-align: left;
            }
            .talla-label {
                min-width: 100px;
                font-size: 0.85rem;
            }
        }
    </style>

    <div class="reviews-wrap">

        {{-- ✅ Notificación --}}
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- 🔹 Encabezado --}}
        <div class="reviews-header">
            <h2><i class="fas fa-star"></i> Reseñas de tus productos</h2>
            <a href="{{ route('vendedor.dashboard') }}">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </a>
        </div>

        {{-- 🔹 Promedio general --}}
        <div class="average-box">
            <div class="average-score">{{ number_format($promedio ?? 0, 1) }}</div>
            <div class="average-info">
                <div class="average-stars">
                    @php
                        $valorPromedio = $promedio ?? 0;
                        $estrellas_llenas = floor($valorPromedio);
                        $tiene_media = ($valorPromedio - $estrellas_llenas) >= 0.5;
                    @endphp

                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $estrellas_llenas)
                            <span class="star">★</span>
                        @elseif($i == $estrellas_llenas + 1 && $tiene_media)
                            <span class="star">★</span>
                        @else
                            <span class="star" style="color: rgba(255,255,255,0.3);">★</span>
                        @endif
                    @endfor
                </div>
                <span>Basado en {{ $totalReseñas ?? 0 }} reseñas verificadas</span>
            </div>
        </div>

        {{-- 🔹 Métricas --}}
        <div class="insights-grid">
            <div class="insight-card">
                <span>Uso diario</span>
                <strong>{{ number_format($promediosCategoria['uso'] ?? 0, 1) }}</strong>
                <small>Promedio de usabilidad</small>
            </div>
            <div class="insight-card">
                <span>Comodidad</span>
                <strong>{{ number_format($promediosCategoria['comodidad'] ?? 0, 1) }}</strong>
                <small>Percepción de confort</small>
            </div>
            <div class="insight-card">
                <span>Duración</span>
                <strong>{{ number_format($promediosCategoria['duracion'] ?? 0, 1) }}</strong>
                <small>Resistencia en el tiempo</small>
            </div>
        </div>

        {{-- 🔹 Distribución de tallas --}}
        @if(!empty($tallaDistribucion) && collect($tallaDistribucion)->sum() > 0)
            <div class="talla-bars">
                <h3><i class="fas fa-ruler-horizontal"></i> Distribución de tallas percibidas</h3>
                @foreach($tallaDistribucion as $clave => $cantidad)
                    @php
                        $total = collect($tallaDistribucion)->sum();
                        $porcentaje = round(($cantidad / $total) * 100);
                        $label = match($clave){
                            'pequena' => 'Más pequeña',
                            'exacta' => 'Talla exacta',
                            'grande' => 'Más grande',
                            default => ucfirst($clave)
                        };
                    @endphp
                    <div class="talla-row">
                        <span class="talla-label">{{ $label }}</span>
                        <div class="talla-progress">
                            <span style="width:{{ $porcentaje }}%"></span>
                        </div>
                        <span class="talla-percentage">{{ $porcentaje }}%</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 🔹 Reseñas --}}
        @if($reseñas->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Aún no tienes reseñas.</p>
            </div>
        @else
            @foreach($reseñas as $r)
                @php
                    $agrupados = $r->aspectos_agrupados;
                    $positivos = collect($agrupados['positivos'] ?? []);
                    $alertas = collect($agrupados['alertas'] ?? []);
                    $otros = collect($agrupados['otros'] ?? []);
                    $hasScores = $r->uso_score || $r->comodidad_score || $r->duracion_score;

                    $rating = floatval($r->estrellas);
                    $estrellas_completas = floor($rating);
                    $tiene_media_estrella = ($rating - $estrellas_completas) >= 0.5;
                @endphp
                <article class="review-card">
                    <header class="review-card__header">
                        <div>
                            <div class="review-card__stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $estrellas_completas)
                                        <span class="star">★</span>
                                    @elseif($i == $estrellas_completas + 1 && $tiene_media_estrella)
                                        <span class="star-half">★</span>
                                    @else
                                        <span class="star-empty">★</span>
                                    @endif
                                @endfor
                            </div>
                            <h3 class="review-card__title">{{ $r->resumen_titular ?? 'Reseña sin título' }}</h3>
                            <p class="review-card__product">{{ $r->producto->nombre ?? 'Producto eliminado' }}</p>
                        </div>
                        <div class="review-card__meta">
                            <span><i class="fas fa-user"></i> {{ $r->cliente->name ?? 'Cliente desconocido' }}</span><br>
                            <small>{{ $r->created_at?->format('d M, Y') }}</small>
                        </div>
                    </header>

                    <p class="review-card__comment">
                        {{ $r->comentario ?: 'El cliente no dejó comentarios adicionales.' }}
                    </p>

                    {{-- Detalles --}}
                    <div class="review-card__panel">
                        <h4><i class="fas fa-list-check"></i> Aspectos Destacados</h4>
                        <ul>
                            @if($positivos->isEmpty() && $alertas->isEmpty())
                                <li><em style="color: var(--gris-texto);">Sin comentarios específicos</em></li>
                            @else
                                @foreach($positivos as $p)
                                    <li>
                                        <i class="fas fa-check-circle" style="color:#28a745"></i>
                                        {{ $p['label'] }}
                                    </li>
                                @endforeach
                                @foreach($alertas as $a)
                                    <li>
                                        <i class="fas fa-exclamation-triangle" style="color:#dc3545"></i>
                                        {{ $a['label'] }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    {{-- Métricas --}}
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

                    {{-- Imágenes --}}
                    @if(isset($r->imagenes) && $r->imagenes->count() > 0)
                        <div class="review-gallery">
                            @foreach($r->imagenes as $img)
                                <a href="{{ asset('storage/'.$img->ruta) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$img->ruta) }}" alt="Imagen de reseña">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Respuesta --}}
                    @if($r->respuesta_vendedor)
                        <div class="reply-box">
                            <strong><i class="fas fa-reply"></i> Tu respuesta:</strong>
                            <p>{{ $r->respuesta_vendedor }}</p>
                        </div>
                    @else
                        <form action="{{ route('vendedor.reseñas.responder', $r->id) }}" method="POST" class="reply-form">
                            @csrf
                            <textarea name="respuesta_vendedor" placeholder="Escribe una respuesta pública al cliente..." required></textarea>
                            <button type="submit">
                                <i class="fas fa-paper-plane"></i> Responder
                            </button>
                        </form>
                    @endif
                </article>
            @endforeach
        @endif
    </div>
@endsection
