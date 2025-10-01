@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #800020;
        }

        .form-container h2 {
            text-align: center;
            color: #800020;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            color: #4d0014;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #800020;
            outline: none;
        }

        .btn {
            padding: 10px 15px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-success {
            background-color: #800020;
            color: #fff;
            margin-right: 10px;
        }

        .btn-success:hover {
            background-color: #4d0014;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #495057;
        }
    </style>

    <div class="form-container">
        <h2>Registrar Nuevo Repartidor</h2>

        <form action="{{ route('admin.repartidores.store') }}" method="POST">
            @csrf

            <label for="nombre">Nombre completo:</label>
            <input type="text" name="nombre" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required minlength="6">

            <label for="estado">Estado:</label>
            <select name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Registrar
            </button>

            <a href="{{ route('admin.repartidores.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
    </div>
@endsection
