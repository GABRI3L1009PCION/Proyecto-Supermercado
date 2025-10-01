<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de entrega | Supermercado Atlantia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/LogoAtlan.png') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .formulario-entrega {
            background-color: #fff;
            padding: 2rem;
            max-width: 500px;
            margin: 3rem auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .formulario-entrega img {
            display: block;
            margin: 0 auto 1rem;
            height: 80px;
        }

        .formulario-entrega h1 {
            text-align: center;
            color: #116334;
            margin-bottom: 1.5rem;
        }

        .formulario-entrega .form-group {
            margin-bottom: 1.2rem;
        }

        .formulario-entrega label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }

        .formulario-entrega input[type="text"],
        .formulario-entrega input[type="tel"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        .formulario-entrega .btn-submit {
            background-color: #16a34a;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .formulario-entrega .btn-submit:hover {
            background-color: #116334;
        }

        .alert-success {
            background: #e7fce9;
            color: #116334;
            padding: 0.7rem;
            border-radius: 7px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: bold;
        }

        footer {
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            padding: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<div class="formulario-entrega">

    <img src="{{ asset('img/LogoAtlan.png') }}" alt="Supermercado Atlantia">

    <h1>Datos de entrega</h1>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('cliente.guardar_detalle_entrega') }}">
        @csrf

        <!-- Dirección -->
        <div class="form-group">
            <label for="direccion">Dirección de entrega *</label>
            <input type="text" name="direccion" id="direccion"
                   value="{{ old('direccion', $detalle->direccion ?? '') }}"
                   placeholder="Ingrese su dirección completa" required>
            @error('direccion')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono de contacto *</label>
            <input type="tel" name="telefono" id="telefono"
                   value="{{ old('telefono', $detalle->telefono ?? '') }}"
                   placeholder="Ingrese su número de teléfono" required>
            @error('telefono')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- NIT -->
        <div class="form-group">
            <label for="nit">NIT (para factura) (opcional)</label>
            <input type="text" name="nit" id="nit"
                   value="{{ old('nit', $detalle->nit ?? '') }}"
                   placeholder="Ingrese su NIT si desea factura">
            @error('nit')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Razón Social -->
        <div class="form-group">
            <label for="razon_social">Razón social (opcional)</label>
            <input type="text" name="razon_social" id="razon_social"
                   value="{{ old('razon_social', $detalle->razon_social ?? '') }}"
                   placeholder="Ingrese la razón social para la factura">
            @error('razon_social')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Botón enviar -->
        <button type="submit" class="btn-submit">Guardar y continuar</button>
    </form>
</div>

<footer>
    Supermercado Atlantia &copy; 2025. Todos los derechos reservados.
</footer>

</body>
</html>
