{{-- resources/views/contact/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
    <style>
        /* Paleta vino tinto (sin custom properties para evitar warnings del linter) */
        .contact-hero{
            background:linear-gradient(135deg,#4d0014,#800020);
            color:#fff;padding:3rem 1rem;text-align:center
        }
        .contact-hero h1{margin:0 0 .5rem 0;font-weight:800;letter-spacing:.2px}
        .contact-hero .actions{display:flex;gap:.6rem;flex-wrap:wrap;justify-content:center;margin-top:1rem}
        .btn-hero{
            background:#fff;color:#4d0014;padding:.7rem 1.1rem;border-radius:999px;
            text-decoration:none;font-weight:700;box-shadow:0 8px 18px rgba(0,0,0,.15);
            border:2px solid #ffffff22
        }
        .btn-hero:hover{opacity:.95}

        .container-contact{max-width:1100px;margin:2rem auto;padding:0 1rem}
        .top-actions{display:flex;justify-content:flex-start;margin-bottom:1rem}
        .btn-back{
            display:inline-flex;align-items:center;gap:.55rem;
            background:#f9e5eb;color:#800020;border:1px solid #e6c4cf;border-radius:10px;
            padding:.55rem .9rem;font-weight:800;text-decoration:none
        }
        .btn-back:hover{background:#f2cad4}

        .grid{display:grid;grid-template-columns:1fr 2fr;gap:1rem}
        @media (max-width: 900px){ .grid{grid-template-columns:1fr} }

        .card{background:#ffffff;border:1px solid #f0dbe1;border-radius:14px;box-shadow:0 8px 20px rgba(16,16,32,.06)}
        .card .body{padding:1.1rem}

        .title{font-weight:800;margin:.1rem 0 1rem 0;color:#800020}
        .line{margin:.35rem 0}
        .muted{color:#5b5b6a;font-size:.93rem}

        .map{position:relative;overflow:hidden;border-radius:10px;aspect-ratio:4/3}
        .map iframe{width:100%;height:100%;border:0}

        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
        .form-grid .full{grid-column:1/-1}
        label{font-weight:700;font-size:.95rem}
        input,select,textarea{
            width:100%;padding:.65rem .75rem;border:1px solid #d8c2c9;border-radius:10px;background:#fff;font:inherit
        }
        input:focus,select:focus,textarea:focus{
            outline:none;border-color:#800020;box-shadow:0 0 0 4px rgba(128,0,32,.08)
        }
        textarea{min-height:140px;resize:vertical}

        .chk{display:flex;gap:.6rem;align-items:flex-start}
        .btn-primary{
            background:#800020;border:none;color:#fff;padding:.75rem 1.2rem;border-radius:10px;
            font-weight:800;cursor:pointer
        }
        .btn-primary:hover{background:#4d0014}
        .quick-note{margin-left:.7rem;color:#666}

        .alert{padding:.9rem 1rem;border-radius:10px;margin-bottom:1rem}
        .alert-success{background:#e8fff0;border:1px solid #b6f2cd;color:#135c2f}
        .alert-danger{background:#ffefef;border:1px solid #ffd1d1;color:#7a1f1f}

        .mini-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1.2rem}
        @media (max-width: 900px){ .mini-cards{grid-template-columns:1fr} }
        .mini{padding:1rem;border:1px solid #f0dbe1;border-radius:14px;background:#fff}
        .mini h4{margin:.3rem 0 .4rem 0;color:#800020}
        .mini a{color:#800020;font-weight:700;text-decoration:none}
        .mini a:hover{text-decoration:underline}

        .faq{margin-top:2rem}
        details{border:1px solid #f0dbe1;border-radius:12px;background:#fff;padding:.7rem 1rem}
        details+details{margin-top:.6rem}
        summary{cursor:pointer;font-weight:800;color:#4d0014}
    </style>

    <div class="contact-hero">
        <h1>¿Necesitas ayuda? ¡Hablemos!</h1>
        <p>En {{ $business['brand'] }} respondemos en 24–48 h hábiles.</p>
        <div class="actions">
            <a class="btn-hero" target="_blank" href="https://wa.me/{{ $business['whatsapp'] }}?text=Hola%20tengo%20una%20consulta">WhatsApp</a>
            <a class="btn-hero" href="tel:{{ preg_replace('/\s+/', '', $business['phone']) }}">Llamar</a>
            <a class="btn-hero" href="#form-contacto">Escríbenos</a>
            <a class="btn-hero" target="_blank" href="https://www.google.com/maps/search/{{ urlencode($business['map_query']) }}">Cómo llegar</a>
        </div>
    </div>

    <div class="container-contact">

        {{-- Botón Regresar --}}
        <div class="top-actions">
            <a href="{{ url()->previous() }}" class="btn-back"
               onclick="event.preventDefault(); if (history.length > 1) { history.back(); } else { window.location.href='{{ route('cliente.productos') }}'; }">
                ← Regresar
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los campos:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid">
            {{-- Columna izquierda: info --}}
            <div class="card">
                <div class="body">
                    <h3 class="title">Datos de contacto</h3>
                    <div class="line"><strong>Tel:</strong> <a href="tel:{{ preg_replace('/\s+/', '', $business['phone']) }}">{{ $business['phone'] }}</a></div>
                    <div class="line"><strong>Email:</strong> <a href="mailto:{{ $business['email'] }}">{{ $business['email'] }}</a></div>
                    <div class="line"><strong>Dirección:</strong> {{ $business['address'] }}</div>
                    <div class="line">
                        <strong>Horarios:</strong>
                        <ul class="muted" style="margin:.4rem 0 0 1rem;">
                            @foreach($business['hours'] as $h) <li>{{ $h }}</li> @endforeach
                        </ul>
                    </div>
                    <div class="map" style="margin-top:1rem;">
                        <iframe src="https://www.google.com/maps?q={{ urlencode($business['map_query']) }}&output=embed" loading="lazy"></iframe>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: formulario --}}
            <div class="card" id="form-contacto">
                <div class="body">
                    <h3 class="title">Envíanos un mensaje</h3>
                    <form method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="form-grid">
                            <div>
                                <label>Nombre completo *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div>
                                <label>Correo electrónico *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div>
                                <label>Teléfono</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+502 5xxx xxxx">
                            </div>
                            <div>
                                <label>Motivo *</label>
                                <select name="type" required>
                                    <option value="">Selecciona…</option>
                                    <option value="soporte"      {{ old('type')=='soporte'?'selected':'' }}>Pedido / Soporte</option>
                                    <option value="facturacion"  {{ old('type')=='facturacion'?'selected':'' }}>Facturación FEL</option>
                                    <option value="devoluciones" {{ old('type')=='devoluciones'?'selected':'' }}>Devoluciones</option>
                                    <option value="proveedor"    {{ old('type')=='proveedor'?'selected':'' }}>Ser proveedor</option>
                                    <option value="mayorista"    {{ old('type')=='mayorista'?'selected':'' }}>Mayoristas / Convenios</option>
                                    <option value="empleo"       {{ old('type')=='empleo'?'selected':'' }}>Bolsa de trabajo</option>
                                    <option value="sugerencia"   {{ old('type')=='sugerencia'?'selected':'' }}>Sugerencias / Quejas</option>
                                </select>
                            </div>
                            <div>
                                <label>Asunto</label>
                                <input type="text" name="subject" value="{{ old('subject') }}">
                            </div>
                            <div>
                                <label>No. de pedido</label>
                                <input type="text" name="order_number" value="{{ old('order_number') }}">
                            </div>
                            <div class="full">
                                <label>Mensaje *</label>
                                <textarea name="message" required>{{ old('message') }}</textarea>
                            </div>
                            <div>
                                <label>Adjuntar (opcional) — PDF/JPG/PNG (máx. 4MB)</label>
                                <input type="file" name="attachment">
                            </div>
                            <div>
                                <label>Verificación</label>
                                <div class="muted" style="border:1px dashed #ccc;border-radius:10px;padding:.8rem">Aquí va el widget de reCAPTCHA (si lo usas).</div>
                            </div>
                            <div class="full chk">
                                <input type="checkbox" id="consent" name="consent" {{ old('consent') ? 'checked' : '' }} required>
                                <label for="consent" class="muted">Acepto la <a href="{{ url('/privacidad') }}" target="_blank">política de privacidad</a> y el uso de mis datos para responder esta solicitud.</label>
                            </div>
                            <div class="full" style="display:flex;align-items:center;gap:.7rem;flex-wrap:wrap">
                                <button class="btn-primary" type="submit">Enviar mensaje</button>
                                <span class="quick-note muted">Tiempo de respuesta: 24–48 h hábiles</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Atajos --}}
        <div class="mini-cards">
            <div class="mini">
                <h4>Clientes</h4>
                <p class="muted">Seguimiento de pedido, cambios/devoluciones y métodos de pago.</p>
                <a href="{{ url('/ayuda/clientes') }}">Ver ayuda</a>
            </div>
            <div class="mini">
                <h4>Facturación FEL</h4>
                <p class="muted">Requisitos: NIT, nombre fiscal, correo y número de pedido.</p>
                <a href="{{ url('/facturacion') }}">Solicitar factura</a>
            </div>
            <div class="mini">
                <h4>Proveedores / Mayoristas</h4>
                <p class="muted">Asóciate con nosotros o pide una cotización.</p>
                <a href="{{ url('/proveedores') }}">Postular</a>
            </div>
        </div>

        {{-- FAQ --}}
        <div class="faq">
            <h3 class="title">Preguntas frecuentes</h3>
            <details open>
                <summary>¿Cómo rastreo mi pedido?</summary>
                <div class="muted">Ingresa a “Mi cuenta → Mis pedidos” y abre el pedido para ver el estado y el tracking si aplica.</div>
            </details>
            <details>
                <summary>¿Zonas y costo de envío?</summary>
                <div class="muted">Cubrimos Puerto Barrios y Santo Tomás. El costo depende de la zona y se calcula en el checkout.</div>
            </details>
            <details>
                <summary>¿Plazos y política de devoluciones?</summary>
                <div class="muted">Cuentas con 3 días naturales desde la entrega para reportar incidencias. Perecederos tienen restricciones.</div>
            </details>
        </div>

        <p class="muted" style="margin-top:1.2rem">
            <strong>Razón social:</strong> Atlantia Supermarket, S.A. — <strong>NIT:</strong> 000000-0 (ejemplo).
        </p>
    </div>
@endsection
