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

        .review-card {
            border-bottom: 1px solid #eee;
            padding: 1.2rem 0;
        }
        .review-card:last-child { border-bottom: none; }

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

        /* 🖼️ Galería de imágenes */
        .review-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .review-gallery img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .review-gallery img:hover { transform: scale(1.05); }

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

        {{-- 🔹 Listado de reseñas --}}
        @if($reseñas->isEmpty())
            <p class="empty-state">Aún no tienes reseñas.</p>
        @else
            @foreach($reseñas as $r)
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <div class="review-customer">
                                <i class="fas fa-user-circle"></i> {{ $r->cliente->name ?? 'Cliente desconocido' }}
                            </div>
                            <div class="review-product">
                                Producto: {{ $r->producto->nombre ?? 'Producto eliminado' }}
                            </div>
                        </div>
                        <div class="review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $r->estrellas ? 'fas fa-star' : 'far fa-star' }}"></i>
                            @endfor
                        </div>
                    </div>

                    <p class="review-text">{{ $r->comentario }}</p>

                    {{-- 🖼️ Galería --}}
                    @if(isset($r->imagenes) && $r->imagenes->count() > 0)
                        <div class="review-gallery">
                            @foreach($r->imagenes as $img)
                                <img src="{{ asset('storage/' . $img->ruta) }}" alt="Imagen de reseña">
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
