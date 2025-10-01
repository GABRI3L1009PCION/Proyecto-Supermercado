@extends('layouts.app')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 30px;">

        <!-- Título -->
        <h1 style="text-align: center; color: #800020; margin-bottom: 30px; font-size: 28px;">
            ✏️ Editar Repartidor
        </h1>

        <!-- Botón Volver -->
        <div style="margin-bottom: 20px;">
            <a href="{{ route('admin.repartidores.index') }}" style="
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor='#495057'" onmouseout="this.style.backgroundColor='#6c757d'">
                ← Volver al Listado
            </a>
        </div>

        <!-- Mensajes de error -->
        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #842029; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>⚠️ Se encontraron errores:</strong>
                <ul style="margin-top: 10px; margin-bottom: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <form action="{{ route('admin.repartidores.update', $repartidor->id) }}" method="POST" style="
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    ">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="nombre" style="display:block; margin-bottom:5px; font-weight:bold; color:#333;">
                    Nombre
                </label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $repartidor->name) }}" required
                       style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="telefono" style="display:block; margin-bottom:5px; font-weight:bold; color:#333;">
                    Teléfono
                </label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $repartidor->telefono) }}" required
                       style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display:block; margin-bottom:5px; font-weight:bold; color:#333;">
                    Correo Electrónico
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $repartidor->email) }}" required
                       style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="estado" style="display:block; margin-bottom:5px; font-weight:bold; color:#333;">
                    Estado
                </label>
                <select id="estado" name="estado" required
                        style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                    <option value="activo" {{ $repartidor->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $repartidor->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div style="margin-bottom: 30px;">
                <label for="password" style="display:block; margin-bottom:5px; font-weight:bold; color:#333;">
                    Contraseña (opcional)
                </label>
                <input type="password" id="password" name="password" placeholder="Dejar vacío para no cambiar"
                       style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            <button type="submit" style="
            background-color: #800020;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor='#5a0017'" onmouseout="this.style.backgroundColor='#800020'">
                💾 Guardar Cambios
            </button>
        </form>
    </div>
@endsection

