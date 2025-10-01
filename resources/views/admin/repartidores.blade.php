@extends('layouts.app')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 30px;">

        <!-- Título -->
        <h1 style="text-align: center; color: #800020; margin-bottom: 30px; font-size: 28px;">
            🛵 Listado de Repartidores
        </h1>

        <!-- Botón crear nuevo repartidor -->
        <div style="margin-bottom: 20px; text-align: left;">
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
        </div>

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
                <th style="padding: 12px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($repartidores as $repartidor)
                <tr>
                    <td style="padding: 12px;">{{ $repartidor->id }}</td>
                    <td style="padding: 12px;">{{ $repartidor->nombre }}</td>
                    <td style="padding: 12px;">{{ $repartidor->telefono }}</td>
                    <td style="padding: 12px;">{{ ucfirst($repartidor->estado) }}</td>
                    <td style="padding: 12px;">
                        <a href="#" style="color: #0066cc; text-decoration: none;">Editar</a> |
                        <form action="{{ route('admin.repartidores.destroy', $repartidor->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Eliminar repartidor?')" style="color: #c0392b; border: none; background: none; cursor: pointer;">
                                Eliminar
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
