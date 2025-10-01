<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión | Supermercado Atlantia</title>
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

        .login-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container img {
            height: 100px;
            margin-bottom: 1rem;
        }

        .login-container h1 {
            color: var(--vino);
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .login-container form {
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

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 1rem;
            text-align: left;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: var(--vino);
            box-shadow: 0 0 0 3px rgba(128, 0, 32, 0.2);
            outline: none;
        }

        input::placeholder {
            color: #aaa;
            font-style: italic;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .remember-me input {
            margin-right: 0.5rem;
        }

        .btn-login {
            background: var(--vino);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }

        .btn-login:hover {
            background: var(--vino-oscuro);
        }

        .forgot-password, .register-link {
            display: block;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: var(--vino-oscuro);
            text-align: center;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-password:hover, .register-link:hover {
            color: var(--vino);
        }

        footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #888;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem 1.2rem;
            }

            .login-container img {
                height: 80px;
            }

            .login-container h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div style="text-align: center; margin-bottom: -40px;">
        <img src="{{ asset('img/LogoAtlan.png') }}" alt="Supermercado Atlantia">
    </div>

    <h1>Iniciar sesión</h1>

    @if (session('status'))
        <div style="background: var(--vino-claro); color: var(--vino-oscuro); padding: 0.7rem; border-radius: 7px; margin-bottom: 1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="Ingrese su correo electrónico">
            @error('email')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required autocomplete="current-password"
                   placeholder="Ingrese su contraseña">
            @error('password')
            <div style="color: red; font-size: 0.85rem; margin-top: 0.4rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="remember-me">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Recuérdame</label>
        </div>

        <button type="submit" class="btn-login">Iniciar sesión</button>

        @if (Route::has('password.request'))
            <a class="forgot-password" href="{{ route('password.request') }}">
                ¿Olvidaste tu contraseña?
            </a>
        @endif

        @if (Route::has('register'))
            <a class="register-link" href="{{ route('register') }}">
                ¿No tienes cuenta? Regístrate aquí
            </a>
        @endif
    </form>
</div>

<footer>
    Supermercado Atlantia &copy; 2025. Todos los derechos reservados.
</footer>

</body>
</html>
