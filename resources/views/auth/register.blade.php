<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse | Supermercado Atlantia</title>
    <link rel="icon" href="{{ asset('img/LogoAtlan.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --vino: #800020;
            --vino-oscuro: #4b0011;
            --vino-claro: #fbe8ee;
            --gris-fondo: #f4f4f4;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--vino-claro);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .register-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .register-container img {
            height: 100px;
            margin-bottom: 1rem;
        }

        .register-container h1 {
            color: var(--vino);
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .register-container form {
            text-align: left;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            font-weight: bold;
            color: var(--vino-oscuro);
            display: block;
            margin-bottom: 0.4rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--vino);
            box-shadow: 0 0 0 3px rgba(128, 0, 32, 0.2);
            outline: none;
        }

        input::placeholder {
            color: #aaa;
            font-style: italic;
        }

        .btn-register {
            background: var(--vino);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-register:hover {
            background: var(--vino-oscuro);
        }

        .login-link {
            display: block;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: var(--vino-oscuro);
            text-align: center;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-link:hover {
            color: var(--vino);
        }

        footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #888;
            text-align: center;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 1.5rem 1.2rem;
            }

            .register-container img {
                height: 80px;
            }

            .register-container h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <div style="text-align: center; margin-bottom: -40px;">
        <img src="{{ asset('img/LogoAtlan.png') }}" alt="Supermercado Atlantia">
    </div>

    <h1>Crear cuenta</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nombre completo</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Ingrese su nombre completo">
            @error('name')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="Ingrese su correo electrónico">
            @error('email')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required autocomplete="new-password"
                   placeholder="Cree una contraseña segura">
            @error('password')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                   placeholder="Confirme su contraseña">
            @error('password_confirmation')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-register">Registrarse</button>

        <a class="login-link" href="{{ route('login') }}">
            ¿Ya tienes cuenta? Inicia sesión aquí
        </a>
    </form>
</div>

<footer>
    Supermercado Atlantia &copy; 2025. Todos los derechos reservados.
</footer>

</body>
</html>
