@extends('layouts.app')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 30px;">

        <!-- Título -->
        <h1 style="text-align: center; color: #800020; margin-bottom: 30px; font-size: 28px;">
            🎟️ Cupones y Promociones
        </h1>

        <!-- Botones de acción -->
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
            <a href="#" style="
                background-color: #800020;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: bold;
            " onmouseover="this.style.backgroundColor='#5a0017'" onmouseout="this.style.backgroundColor='#800020'">
                + Crear nuevo cupón
            </a>
            <a href="{{ route('admin.panel') }}" style="
                background-color: #6c757d;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: bold;
            ">
                ← Volver al panel
            </a>
        </div>

        <!-- Tabla -->
        <table style="
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        ">
            <thead style="background-color: #f3f3f3;">
            <tr style="color: #333;">
                <th style="padding: 12px;">Código</th>
                <th style="padding: 12px;">Descripción</th>
                <th style="padding: 12px;">Descuento</th>
                <th style="padding: 12px;">Válido hasta</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <!-- Aquí irán los registros dinámicamente -->
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; color: #777;">
                    No hay cupones ni promociones registradas.
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
