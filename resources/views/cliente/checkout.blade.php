<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Supermercado Atlantia</title>
    <link rel="icon" href="{{ asset('img/LogoAtlan.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root{
            --vino:#800020; --vino-oscuro:#4d0014; --vino-claro:#f9e5eb;
            --gris:#f4f4f4; --borde:#e9e9ea; --texto:#222; --ok:#22c55e;
            --sombra:0 4px 12px rgba(0,0,0,.08); --radio:12px;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;background:#f8f9fa;color:var(--texto);line-height:1.5}

        .checkout-wrapper{max-width:100%;margin:0 auto;padding:16px}
        .checkout-container{background:#fff;border-radius:var(--radio);box-shadow:var(--sombra);overflow:hidden;margin-bottom:24px}

        /* Header */
        .checkout-header{display:flex;align-items:center;gap:12px;padding:20px;background:var(--vino);color:#fff}
        .checkout-header img{height:48px;width:auto;object-fit:contain}
        .checkout-title{font-weight:700;font-size:1.25rem}

        /* Stepper */
        .stepper-container{padding:16px 20px;background:#fff;border-bottom:1px solid var(--borde)}
        .stepper{display:flex;justify-content:space-between;position:relative;margin:0 auto;max-width:420px}
        .stepper::before{content:'';position:absolute;top:50%;left:0;right:0;height:2px;background:var(--borde);transform:translateY(-50%);z-index:1}
        .step{position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;width:80px}
        .step-dot{width:32px;height:32px;border-radius:50%;background:#fff;border:2px solid var(--borde);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;margin-bottom:6px;transition:.3s}
        .step-label{font-size:.75rem;font-weight:700;color:#6b7280;text-align:center}
        .step.active .step-dot{background:var(--vino);border-color:var(--vino);color:#fff}
        .step.active .step-label{color:var(--vino-oscuro)}
        .step.done .step-dot{background:var(--ok);border-color:var(--ok);color:#fff}

        /* Secciones */
        .step-section{display:none;padding:20px}
        .step-section.active{display:block}
        .section-title{display:flex;align-items:center;gap:10px;font-size:1.125rem;font-weight:700;color:var(--vino-oscuro);margin-bottom:20px}
        .section-title i{color:var(--vino)}

        /* Carrito */
        .cart-items{display:flex;flex-direction:column;gap:16px;margin-bottom:24px}
        .cart-item{display:flex;padding:16px;background:#fafafa;border-radius:var(--radio);border:1px solid var(--borde)}
        .item-info{flex:1}
        .item-name{font-weight:700;margin-bottom:6px;color:var(--vino-oscuro)}
        .item-details{display:flex;justify-content:space-between;margin-top:10px}
        .item-price{font-weight:700;color:var(--vino)}
        .item-quantity{display:flex;align-items:center;gap:10px}
        .quantity-btn{width:32px;height:32px;border-radius:50%;border:1px solid var(--borde);background:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer}
        .quantity-value{font-weight:700;min-width:20px;text-align:center}
        .item-subtotal{display:flex;align-items:center;justify-content:space-between;margin-top:10px;padding-top:10px;border-top:1px dashed var(--borde)}
        .delete-btn{background:none;border:none;color:#dc2626;font-size:18px;cursor:pointer;padding:4px}
        .cart-total{padding:16px;background:var(--vino-claro);border-radius:var(--radio);margin-top:20px;text-align:right;font-weight:800;font-size:1.125rem;color:var(--vino-oscuro)}

        /* Formularios */
        .form-group{margin-bottom:20px}
        .form-label{display:block;font-weight:700;margin-bottom:8px;color:var(--vino-oscuro)}
        .form-input,.form-select{width:100%;padding:14px 16px;border:1px solid var(--borde);border-radius:var(--radio);font-size:16px;transition:border-color .2s}
        .form-input:focus,.form-select:focus{outline:none;border-color:var(--vino);box-shadow:0 0 0 3px rgba(128,0,32,.1)}

        /* Mapa */
        #mapa{width:100%;height:240px;border-radius:var(--radio);border:1px solid var(--borde)}

        /* Botones */
        .btn-group{display:flex;gap:12px;margin-top:24px;flex-wrap:wrap}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 20px;border-radius:var(--radio);font-weight:700;font-size:16px;cursor:pointer;transition:.2s;border:none;text-decoration:none;flex:1;min-width:140px}
        .btn-primary{background:linear-gradient(135deg,var(--vino) 0%,#6d0020 100%);color:#fff}
        .btn-primary:hover{filter:brightness(1.05);transform:translateY(-1px);box-shadow:0 4px 8px rgba(0,0,0,.1)}
        .btn-secondary{background:#fff;color:var(--texto);border:1px solid var(--borde)}

        /* Modales */
        .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px}
        .modal-content{background:#fff;border-radius:var(--radio);padding:24px;max-width:420px;width:100%;text-align:center;box-shadow:0 10px 25px rgba(0,0,0,.2)}
        .modal-title{font-size:1.25rem;font-weight:800;margin-bottom:12px;color:var(--vino-oscuro)}
        .modal-text{margin-bottom:20px;color:#6b7280}

        .checkout-footer{text-align:center;padding:20px;color:#6b7280;font-size:.875rem}

        /* Errores */
        .error-message{color:#dc2626;font-size:.875rem;margin-top:6px;display:block}
        .alert-error{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:16px;border-radius:var(--radio);margin:20px}
        .helper-text{display:block;font-size:.85rem;color:#6b7280;margin-top:6px}

        /* === Pago: opciones (NUEVO) === */
        .pay-grid{
            display:grid; gap:14px;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }
        .pay-option{
            position:relative;
            display:grid; grid-template-rows:auto 1fr auto;
            gap:12px; min-height:180px; padding:18px;
            border:1px solid var(--borde); border-radius:14px;
            background:#fff; cursor:pointer;
            transition:box-shadow .2s, border-color .2s, transform .08s;
        }
        .pay-option:hover{box-shadow:var(--sombra);transform:translateY(-1px)}
        .pay-option.selected{border-color:var(--vino);box-shadow:0 6px 18px rgba(128,0,32,.12)}

        /* Radio accesible personalizado */
        .pay-option input[type="radio"]{
            appearance:none;-webkit-appearance:none;
            width:20px;height:20px;border:2px solid var(--borde);border-radius:50%;
            position:absolute;top:16px;left:16px;display:grid;place-content:center;
            background:#fff;outline:none;transition:border-color .2s, box-shadow .2s;
        }
        .pay-option input[type="radio"]::after{
            content:"";width:10px;height:10px;border-radius:50%;background:var(--vino);
            transform:scale(0);transition:transform .15s ease-out;
        }
        .pay-option input[type="radio"]:checked{border-color:var(--vino);box-shadow:0 0 0 3px rgba(128,0,32,.12)}
        .pay-option input[type="radio"]:checked::after{transform:scale(1)}
        .pay-option input[type="radio"]:focus-visible{box-shadow:0 0 0 3px rgba(59,130,246,.35)}

        .pay-head{display:flex;align-items:center;gap:12px;padding-left:30px}
        .pay-title{font-weight:800;font-size:1.25rem;color:var(--vino-oscuro);flex:1 1 auto;line-height:1.2}
        .pay-details-btn{flex:0 0 auto;min-width:auto;padding:10px 14px;border:1px solid var(--borde);background:#fff;border-radius:10px}
        .pay-option.selected .pay-details-btn{border-color:var(--vino)}

        .pay-middle{display:flex;align-items:center;gap:12px;padding-left:30px}
        .pay-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--vino-claro);color:var(--vino);font-size:22px}
        .pay-sub{padding-left:30px;color:#6b7280;font-size:.95rem}

        .pay-note{margin-top:10px;background:#f8fafc;border:1px dashed var(--borde);padding:12px;border-radius:10px;color:#475569;display:none}
        .pay-note.show{display:block}

        /* Responsive */
        @media (min-width:768px){
            .checkout-wrapper{padding:24px;max-width:750px}
            #mapa{height:300px}
        }
        @media (min-width:1024px){
            .checkout-wrapper{max-width:900px;padding:32px 0}
            .checkout-container{border-radius:16px}
        }

        /* Facturación condicional */
        .fact-fields{display:none}
    </style>
</head>
<body>
@php
    $coloniasListado = collect($colonias ?? config('geografia.santo_tomas_colonias'));
    $coloniasDataset = $coloniasListado->map(fn ($colonia) => [
        'nombre' => $colonia['nombre'],
        'lat'    => $colonia['lat'],
        'lng'    => $colonia['lng'],
        'zoom'   => $colonia['zoom'] ?? 16,
    ]);
    $mapaCentro = $mapaDefault ?? config('geografia.santo_tomas_default');
@endphp
<div class="checkout-wrapper">
    <div class="checkout-container">
        <!-- Header -->
        <div class="checkout-header">
            <img src="{{ asset('img/LogoAtlan.png') }}" alt="Atlantia">
            <div class="checkout-title">Finaliza tu pedido</div>
        </div>

        <!-- Stepper -->
        <div class="stepper-container">
            <div class="stepper">
                <div class="step active" data-step="1">
                    <div class="step-dot">1</div><div class="step-label">Carrito</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-dot">2</div><div class="step-label">Entrega</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-dot">3</div><div class="step-label">Pago y Facturación</div>
                </div>
            </div>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert-error">
                <strong>Por favor, corrige los siguientes errores:</strong>
                <ul style="margin-top:8px;margin-left:20px;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Paso 1: Carrito -->
        <div id="paso1" class="step-section active">
            <div class="section-title"><i class="fa-solid fa-bag-shopping"></i><span>Resumen de tu carrito</span></div>

            <div class="cart-items">
                @php $total = 0; @endphp
                @foreach ($carrito as $id => $item)
                    @php $subtotal = $item['precio'] * $item['cantidad']; $total += $subtotal; @endphp
                    <div class="cart-item">
                        <div class="item-info">
                            <div class="item-name">{{ $item['nombre'] }}</div>

                            <div class="item-details">
                                <div class="item-price">Q{{ number_format($item['precio'], 2) }}</div>
                                <div class="item-quantity">
                                    <form action="{{ route('carrito.reducir', $id) }}" method="POST" style="display:inline">@csrf
                                        <button type="submit" class="quantity-btn" aria-label="Reducir">-</button>
                                    </form>
                                    <span class="quantity-value">{{ $item['cantidad'] }}</span>
                                    <form action="{{ route('carrito.aumentar', $id) }}" method="POST" style="display:inline">@csrf
                                        <button type="submit" class="quantity-btn" aria-label="Aumentar">+</button>
                                    </form>
                                </div>
                            </div>

                            <div class="item-subtotal">
                                <span>Subtotal: <strong class="item-price">Q{{ number_format($subtotal, 2) }}</strong></span>
                                <form action="{{ route('carrito.eliminar', $id) }}" method="POST" style="display:inline">@csrf
                                    <button type="submit" class="delete-btn" aria-label="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cart-total">Total: Q{{ number_format($total, 2) }}</div>

            <div class="btn-group">
                <a href="{{ route('cliente.productos') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Seguir comprando</a>
                <button class="btn btn-primary" onclick="mostrarModalCarrito()">Continuar con entrega <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>

        {{-- FORM: Paso 2 + 3 --}}
        <form method="POST" action="{{ route('checkout.confirmar') }}" id="checkoutForm">
            @csrf

            <!-- Paso 2: Entrega -->
            <div id="paso2" class="step-section">
                <div class="section-title"><i class="fa-solid fa-truck-fast"></i><span>Datos de entrega</span></div>

                <div class="form-group">
                    <label class="form-label">Dirección *</label>
                    <input type="text" name="direccion" class="form-input" value="{{ old('direccion') }}" required>
                    @error('direccion') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono *</label>
                    <input type="tel" name="telefono" class="form-input" value="{{ old('telefono') }}" required>
                    @error('telefono') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Referencia adicional</label>
                    <input type="text" name="referencia" class="form-input" value="{{ old('referencia') }}">
                    @error('referencia') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="coloniaSelect">Colonia o Barrio *</label>
                    <select name="colonia" id="coloniaSelect" class="form-select" required>
                        <option value="">Selecciona tu colonia o barrio</option>
                        @foreach ($coloniasListado as $colonia)
                            <option value="{{ $colonia['nombre'] }}" @selected(old('colonia')===$colonia['nombre'])>
                                {{ $colonia['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('colonia') <span class="error-message">{{ $message }}</span> @enderror
                    <span class="helper-text">Al elegir una colonia centraremos el mapa automáticamente en la zona seleccionada.</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Ubicación en el mapa</label>
                    <div id="mapa"></div>
                    <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                    <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                    @error('lat') <span class="error-message">{{ $message }}</span> @enderror
                    @error('lng') <span class="error-message">{{ $message }}</span> @enderror
                    <span class="helper-text">Después de centrar el mapa, mueve el pin haciendo clic donde exactamente deseas recibir tu pedido.</span>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="irAPaso(1)"><i class="fa-solid fa-arrow-left"></i> Volver al carrito</button>
                    <button type="button" class="btn btn-primary" onclick="mostrarModalEntrega()">Siguiente <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Paso 3: Pago y Facturación -->
            <div id="paso3" class="step-section">
                <div class="section-title"><i class="fa-solid fa-credit-card"></i><span>Método de pago</span></div>

                <!-- Opciones de pago -->
                <div class="pay-grid" id="payGrid">
                    <!-- Efectivo -->
                    <label class="pay-option" data-pay="efectivo">
                        <input type="radio" name="metodo_pago" value="efectivo" {{ old('metodo_pago')==='efectivo' ? 'checked' : '' }} required>
                        <div class="pay-head">
                            <div class="pay-title">Pagar en efectivo</div>
                            <button type="button" class="pay-details-btn" onclick="abrirModal('modalEfectivo')">Detalles</button>
                        </div>
                        <div class="pay-middle">
                            <div class="pay-icon"><i class="fa-solid fa-money-bill-1-wave"></i></div>
                        </div>
                        <div class="pay-sub">Cancela al recibir tu pedido.</div>
                    </label>

                    <!-- Tarjeta -->
                    <label class="pay-option" data-pay="tarjeta">
                        <input type="radio" name="metodo_pago" value="tarjeta" {{ old('metodo_pago')==='tarjeta' ? 'checked' : '' }}>
                        <div class="pay-head">
                            <div class="pay-title">Pagar con tarjeta</div>
                            <button type="button" class="pay-details-btn" onclick="abrirModal('modalTarjeta')">Detalles</button>
                        </div>
                        <div class="pay-middle">
                            <div class="pay-icon"><i class="fa-solid fa-credit-card"></i></div>
                        </div>
                        <div class="pay-sub">Procesaremos el pago de forma segura.</div>
                    </label>

                    <!-- Transferencia -->
                    <label class="pay-option" data-pay="transferencia">
                        <input type="radio" name="metodo_pago" value="transferencia" {{ old('metodo_pago')==='transferencia' ? 'checked' : '' }}>
                        <div class="pay-head">
                            <div class="pay-title">Transferencia / Depósito</div>
                            <button type="button" class="pay-details-btn" onclick="abrirModal('modalTransferencia')">Detalles</button>
                        </div>
                        <div class="pay-middle">
                            <div class="pay-icon"><i class="fa-solid fa-building-columns"></i></div>
                        </div>
                        <div class="pay-sub">Realiza una transferencia bancaria.</div>
                    </label>
                </div>
                @error('metodo_pago') <span class="error-message">{{ $message }}</span> @enderror

                <!-- Nota dinámica -->
                <div id="payNote" class="pay-note"></div>

                <hr style="margin:24px 0;border:none;border-top:1px solid var(--borde)">

                <div class="section-title"><i class="fa-solid fa-file-invoice"></i><span>Datos de facturación (opcional)</span></div>

                <div class="form-group">
                    <label class="form-label">¿Desea factura?</label>
                    <select name="factura" id="factura" class="form-select" required>
                        <option value=""  {{ old('factura')==''  ? 'selected':'' }}>Seleccione</option>
                        <option value="no" {{ old('factura')=='no' ? 'selected':'' }}>No</option>
                        <option value="si" {{ old('factura')=='si' ? 'selected':'' }}>Sí</option>
                    </select>
                    @error('factura') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group fact-fields">
                    <label class="form-label">NIT</label>
                    <input type="text" name="nit" id="nit" class="form-input" value="{{ old('nit') }}">
                    @error('nit') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group fact-fields">
                    <label class="form-label">Razón Social</label>
                    <input type="text" name="razon_social" id="razon_social" class="form-input" value="{{ old('razon_social') }}">
                    @error('razon_social') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group fact-fields">
                    <label class="form-label">Nombre Empresa</label>
                    <input type="text" name="nombre_empresa" id="nombre_empresa" class="form-input" value="{{ old('nombre_empresa') }}">
                    @error('nombre_empresa') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="irAPaso(2)"><i class="fa-solid fa-arrow-left"></i> Volver a entrega</button>
                    <button type="submit" class="btn btn-primary">Confirmar pedido <i class="fa-solid fa-check"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modales de pasos --}}
<div id="modalCarrito" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">✔ Carrito confirmado</h3>
        <p class="modal-text">Pasaremos a los datos de entrega.</p>
        <button class="btn btn-primary" onclick="irAPaso(2, true)">Siguiente</button>
    </div>
</div>

<div id="modalEntrega" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">📦 Datos de entrega guardados</h3>
        <p class="modal-text">Ahora elige cómo deseas pagar y, si quieres, agrega datos de factura.</p>
        <button class="btn btn-primary" onclick="irAPaso(3)">Continuar</button>
    </div>
</div>

{{-- Modales de pago --}}
<div id="modalEfectivo" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">Pago en efectivo</h3>
        <p class="modal-text">El cobro se realizará al momento de recibir tu pedido. Te recomendamos tener el monto preparado.</p>
        <button class="btn btn-primary" onclick="cerrarModal('modalEfectivo')">Entendido</button>
    </div>
</div>

<div id="modalTarjeta" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">Pago con tarjeta</h3>
        <p class="modal-text">Procesaremos tu pago de forma segura. Tras confirmar el pedido, recibirás un enlace/checkout para completar el pago. No almacenamos datos sensibles.</p>
        <button class="btn btn-primary" onclick="cerrarModal('modalTarjeta')">Entendido</button>
    </div>
</div>

<div id="modalTransferencia" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">Transferencia / Depósito</h3>
        <p class="modal-text" style="text-align:left">
            Realiza la transferencia y conserva tu comprobante:<br><br>
            <strong>Banco:</strong> (Banrural)<br>
            <strong>Cuenta:</strong> (0000000000, Monetaria)<br>
            <strong>A nombre de:</strong> Supermercado Atlantia<br>
            <strong>Referencia:</strong> Pedido #<span id="refPedido">{{ $pedidoId ?? '—' }}</span><br><br>
            Una vez confirmado el pago, procesaremos tu pedido.
        </p>
        <button class="btn btn-primary" onclick="cerrarModal('modalTransferencia')">Entendido</button>
    </div>
</div>

<footer class="checkout-footer">
    Supermercado Atlantia &copy; {{ now()->year }}. Todos los derechos reservados.
</footer>

<script>
    // --- Modales genéricos ---
    function abrirModal(id){
        const modal=document.getElementById(id);
        if(!modal) return;
        modal.style.display='flex';
        modal.addEventListener('click',e=>{ if(e.target===modal) cerrarModal(id); });
    }
    function cerrarModal(id){ const m=document.getElementById(id); if(m) m.style.display='none'; }
    function mostrarModalCarrito(){ abrirModal('modalCarrito'); }
    const coloniaSelect = document.getElementById('coloniaSelect');

    function mostrarModalEntrega(){
        const d=document.querySelector('input[name="direccion"]').value.trim();
        const t=document.querySelector('input[name="telefono"]').value.trim();
        const c=coloniaSelect ? coloniaSelect.value : '';
        if(!d||!t||!c){ alert('Por favor, completa todos los campos obligatorios de entrega.'); return; }
        abrirModal('modalEntrega');
    }

    // --- Navegación de pasos ---
    function setStepper(n){
        document.querySelectorAll('.step').forEach((s,i)=>{
            s.classList.remove('active','done');
            if(i+1<n) s.classList.add('done');
            if(i+1===n) s.classList.add('active');
        });
    }
    function irAPaso(n,focusMapa=false){
        cerrarModal('modalCarrito'); cerrarModal('modalEntrega');
        document.querySelectorAll('.step-section').forEach(s=>s.classList.remove('active'));
        document.getElementById('paso'+n).classList.add('active'); setStepper(n);
        if(n===2 && focusMapa){ setTimeout(()=>{ mapa.invalidateSize(); if(marker) mapa.setView(marker.getLatLng(), mapa.getZoom()); },100); }
        window.scrollTo({top:0,behavior:'smooth'});
    }

    // --- Leaflet ---
    const baseCenter=@json($mapaCentro);
    const coloniasData=@json($coloniasDataset);
    const mapa=L.map('mapa').setView([baseCenter.lat, baseCenter.lng], baseCenter.zoom ?? 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(mapa);
    let marker;

    function colocarMarcador(pos, zoom){
        if(!pos) return;
        if(marker) mapa.removeLayer(marker);
        marker=L.marker(pos).addTo(mapa);
        if(zoom){ mapa.setView(pos, zoom); }
        document.getElementById('lat').value=pos.lat;
        document.getElementById('lng').value=pos.lng;
    }

    (function initMarkerFromOld(){
        const latOld=parseFloat(document.getElementById('lat').value);
        const lngOld=parseFloat(document.getElementById('lng').value);
        if(!Number.isNaN(latOld) && !Number.isNaN(lngOld)){
            const pos=L.latLng(latOld,lngOld);
            colocarMarcador(pos, mapa.getZoom());
        }
    })();

    function centrarEnColonia(nombre){
        const colonia=coloniasData.find(c=>c.nombre===nombre);
        if(!colonia) return;
        if(colonia.lat && colonia.lng){
            const pos=L.latLng(colonia.lat, colonia.lng);
            colocarMarcador(pos, colonia.zoom || 16);
        }
    }

    if(coloniaSelect){
        coloniaSelect.addEventListener('change',()=>centrarEnColonia(coloniaSelect.value));
        if(coloniaSelect.value){
            centrarEnColonia(coloniaSelect.value);
        }
    }

    mapa.on('click',function(e){
        colocarMarcador(e.latlng, mapa.getZoom());
    });

    // --- Facturación condicional ---
    function toggleFacturaFields(){
        const need=document.getElementById('factura').value==='si';
        document.querySelectorAll('.fact-fields').forEach(el=>el.style.display=need?'block':'none');
    }
    document.getElementById('factura').addEventListener('change',toggleFacturaFields);
    toggleFacturaFields();

    // --- Pago: UI + nota dinámica ---
    const payGrid=document.getElementById('payGrid');
    const payNote=document.getElementById('payNote');
    const notes={
        efectivo:'El cobro se realizará al momento de recibir tu pedido. ¡Ten el efectivo listo!',
        tarjeta:'Pago con tarjeta mediante un proceso seguro. Tras confirmar, recibirás un enlace o checkout para completar el pago.',
        transferencia:'Realiza una transferencia o depósito y conserva tu comprobante. Procesaremos tu pedido al confirmar el pago.'
    };
    function updatePayUI(){
        document.querySelectorAll('.pay-option').forEach(card=>{
            const input=card.querySelector('input[type="radio"]');
            if(input && input.checked) card.classList.add('selected'); else card.classList.remove('selected');
        });
        const selected=document.querySelector('input[name="metodo_pago"]:checked');
        if(selected){ payNote.textContent=notes[selected.value]||''; payNote.classList.add('show'); }
        else{ payNote.classList.remove('show'); payNote.textContent=''; }
    }
    payGrid.addEventListener('click',function(e){
        const label=e.target.closest('.pay-option'); if(!label) return;
        const input=label.querySelector('input[type="radio"]'); if(input){ input.checked=true; updatePayUI(); }
    });
    document.querySelectorAll('input[name="metodo_pago"]').forEach(r=>r.addEventListener('change',updatePayUI));
    updatePayUI();

    // --- Abrir paso correcto si hay errores ---
    @if ($errors->any())
    (function(){
        const pagoFields=['metodo_pago'];
        const facturaFields=['factura','nit','razon_social','nombre_empresa'];
        const errs=@json($errors->keys());
        let paso=2;
        if(errs.some(k=>pagoFields.includes(k)||facturaFields.includes(k))) paso=3;
        irAPaso(paso, paso===2);
    })();
    @endif
</script>
</body>
</html>
