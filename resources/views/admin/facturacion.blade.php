@extends('layouts.app')

@section('content')
    <div style="max-width: 1100px; margin: 0 auto; padding: 30px;">

        <!-- Título -->
        <h1 style="text-align: center; color: #800020; margin-bottom: 30px; font-size: 28px;">
            🧾 Página de Facturación
        </h1>

        <!-- Botón exportar PDF (futuro uso) -->
        <div style="margin-bottom: 20px; text-align: right;">
            <a href="#" style="
                background-color: #800020;
                color: white;
                padding: 10px 18px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: bold;
                transition: background-color 0.3s ease;
            " onmouseover="this.style.backgroundColor='#5a0017'" onmouseout="this.style.backgroundColor='#800020'">
                📄 Exportar a PDF
            </a>
        </div>

        <!-- Tabla de facturación -->
        <table style="
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        ">
            <thead style="background-color: #f3f3f3;">
            <tr style="color: #333;">
                <th style="padding: 12px;">#Factura</th>
                <th style="padding: 12px;">Cliente</th>
                <th style="padding: 12px;">Fecha</th>
                <th style="padding: 12px;">Total</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <!-- Aquí irán facturas dinámicamente -->
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; color: #777;">
                    No hay facturas registradas por el momento.
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
