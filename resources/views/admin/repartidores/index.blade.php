@extends('layouts.app')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 30px;">

        <!-- Título -->
        <h1 style="text-align: center; color: #800020; margin-bottom: 30px; font-size: 28px;">
            🛵 Listado de Repartidores
        </h1>

        <!-- Botones: Crear y Volver -->
        <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.repartidores.create') }}" style="
            background-color: #800020;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor='#5a0017'" onmouseout="this.style.backgroundColor='#800020'">
                + Registrar nuevo repartidor
            </a>

            <a href="{{ route('admin.panel') }}" style="
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor='#495057'" onmouseout="this.style.backgroundColor='#6c757d'">
                ← Volver al Dashboard
            </a>
        </div>

        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabla de repartidores -->
        <table style="
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    ">
            <thead style="background-color: #f4f4f4;">
            <tr style="text-align: left; color: #333;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Nombre</th>
                <th style="padding: 12px;">Teléfono</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px; text-align: center;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($repartidores as $repartidor)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px;">{{ $repartidor->id }}</td>
                    <td style="padding: 12px;">{{ $repartidor->name }}</td>
                    <td style="padding: 12px;">{{ $repartidor->telefono }}</td>
                    <td style="padding: 12px;">{{ ucfirst($repartidor->estado) }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('admin.repartidores.edit', $repartidor->id) }}" style="
                        color: #007bff;
                        text-decoration: none;
                        font-weight: bold;
                        margin-right: 10px;
                    " onmouseover="this.style.color='#0056b3'" onmouseout="this.style.color='#007bff'">
                            ✏️ Editar
                        </a>
                        <form action="{{ route('admin.repartidores.destroy', $repartidor->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Eliminar repartidor?')" style="
                            color: #c0392b;
                            border: none;
                            background: none;
                            font-weight: bold;
                            cursor: pointer;
                            padding: 0;
                        " onmouseover="this.style.color='#922b21'" onmouseout="this.style.color='#c0392b'">
                                🗑️ Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #777;">
                        No hay repartidores registrados aún.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
