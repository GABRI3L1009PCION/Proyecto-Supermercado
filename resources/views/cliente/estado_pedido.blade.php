<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido - Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --vino-primario: #722F37;
            --vino-secundario: #8C3A44;
            --vino-terciario: #A64D57;
            --vino-claro: #F9F2F3;
            --vino-acento: #D9A6A6;
            --texto-oscuro: #2C181A;
            --texto-claro: #FFFFFF;
        }

        body {
            background-color: #f8f9fa;
            color: var(--texto-oscuro);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--vino-acento);
        }

        h1 {
            color: var(--vino-primario);
            font-weight: 700;
            margin: 0;
        }

        .btn-vino {
            background-color: var(--vino-primario);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .btn-vino:hover {
            background-color: var(--vino-secundario);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(114, 47, 55, 0.3);
        }

        .btn-vino i {
            margin-right: 8px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--vino-primario);
        }

        .card-header {
            border-bottom: 1px solid var(--vino-acento);
            margin-bottom: 20px;
            padding-bottom: 15px;
        }

        .card-header h3 {
            color: var(--vino-primario);
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .card-header h3 i {
            margin-right: 10px;
            color: var(--vino-secundario);
        }

        .info-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .info-list li {
            margin-bottom: 15px;
            padding: 10px 0;
            display: flex;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-list li:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        .info-list strong {
            min-width: 150px;
            color: var(--vino-secundario);
            display: flex;
            align-items: center;
        }

        .info-list strong i {
            margin-right: 8px;
        }

        .badge-estado {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: capitalize;
        }

        .badge-pendiente { background-color: #ffc107; color: #212529; }
        .badge-aceptado { background-color: #17a2b8; color: white; }
        .badge-preparando { background-color: #fd7e14; color: white; }
        .badge-listo { background-color: #007bff; color: white; }
        .badge-entregado { background-color: #28a745; color: white; }
        .badge-rechazado { background-color: #dc3545; color: white; }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--vino-claro);
            color: var(--vino-primario);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--vino-acento);
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .producto-info {
            display: flex;
            flex-direction: column;
        }

        .producto-nombre {
            font-weight: 600;
            color: var(--vino-primario);
        }

        .producto-codigo {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .estado-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .form-select {
            border-radius: 6px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            min-width: 150px;
        }

        .btn-actualizar {
            background-color: var(--vino-primario);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-actualizar:hover {
            background-color: var(--vino-secundario);
        }

        .acciones-rapidas {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn-accion {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background: var(--vino-claro);
            border: 1px solid var(--vino-acento);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .btn-accion:hover {
            background-color: var(--vino-primario);
            color: white;
        }

        .repartidor-section {
            background-color: var(--vino-claro);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .repartidor-select {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .info-list li {
                flex-direction: column;
            }

            .info-list strong {
                min-width: auto;
                margin-bottom: 5px;
            }

            .estado-form {
                flex-direction: column;
                align-items: flex-start;
            }

            .acciones-rapidas {
                flex-direction: column;
            }

            .repartidor-select {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-shopping-bag"></i> Pedido #{{ $pedido->id }}</h1>
        <a href="{{ route('vendedor.dashboard') }}" class="btn-vino">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>
    </div>

    <!-- Información del Pedido -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Información del Pedido</h3>
        </div>
        <ul class="info-list">
            <li>
                <strong><i class="fas fa-user"></i> Cliente:</strong>
                <span>{{ $pedido->cliente->name ?? 'Cindy Picon' }}</span>
            </li>
            <li>
                <strong><i class="fas fa-calendar"></i> Fecha:</strong>
                <span>{{ $pedido->created_at->format('d/m/Y H:i') ?? '17/09/2025 17:31' }}</span>
            </li>
            <li>
                <strong><i class="fas fa-tag"></i> Estado:</strong>
                <span class="badge-estado badge-{{ $pedido->estado_global ?? 'pendiente' }}">
                        {{ $pedido->estado_global ?? 'pendiente' }}
                    </span>
            </li>
            <li>
                <strong><i class="fas fa-map-marker-alt"></i> Dirección:</strong>
                <span>{{ $pedido->direccion_envio['direccion'] ?? 'Barrio el Pueblito Santo Tomas de Castilla, El Rastro (Ref: Casa color melón)' }}</span>
            </li>
            <li>
                <strong><i class="fas fa-phone"></i> Teléfono:</strong>
                <span>{{ $pedido->cliente->telefono ?? '+502 1234-5678' }}</span>
            </li>
            <li>
                <strong><i class="fas fa-envelope"></i> Email:</strong>
                <span>{{ $pedido->cliente->email ?? 'cindy09@gmail.com' }}</span>
            </li>
        </ul>

        <!-- Sección para asignar repartidor -->
        <div class="repartidor-section">
            <h4><i class="fas fa-motorcycle"></i> Asignar Repartidor</h4>
            <div class="repartidor-select">
                <select class="form-select" id="repartidor-select">
                    <option value="">Seleccionar repartidor</option>
                    <option value="vendedor">Yo mismo (Vendedor)</option>
                    <option value="1">Juan Pérez (+502 5555-1234)</option>
                    <option value="2">María García (+502 5555-5678)</option>
                    <option value="3">Carlos López (+502 5555-9012)</option>
                </select>
                <button class="btn-vino" onclick="asignarRepartidor()">
                    <i class="fas fa-user-check"></i> Asignar
                </button>
            </div>
        </div>
    </div>

    <!-- Productos asignados -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-boxes"></i> Productos asignados</h3>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pedidoItems as $item)
                    <tr>
                        <td>
                            <div class="producto-info">
                                <span class="producto-nombre">{{ $item->producto->nombre ?? 'Gloss Dios' }}</span>
                                <span class="producto-codigo">Código: {{ $item->producto->codigo ?? 'GD001' }}</span>
                            </div>
                        </td>
                        <td>{{ $item->cantidad }}</td>
                        <td>Q{{ number_format($item->precio_unitario, 2) }}</td>
                        <td>Q{{ number_format($item->cantidad * $item->precio_unitario, 2) }}</td>
                        <td>
                            @php
                                $estado = $item->fulfillment_status ?? 'accepted';
                                $badgeClass = [
                                    'accepted' => 'badge-aceptado',
                                    'preparing' => 'badge-preparando',
                                    'ready' => 'badge-listo',
                                    'delivered' => 'badge-entregado',
                                    'rejected' => 'badge-rechazado'
                                ][$estado] ?? 'badge-pendiente';
                            @endphp
                            <span class="badge-estado {{ $badgeClass }}">{{ $estado }}</span>
                        </td>
                        <td>
                            <form class="estado-form" action="{{ route('vendedor.pedidoitems.estado', $item->id) }}" method="POST">
                                @csrf
                                <select name="estado" class="form-select" onchange="this.form.submit()">
                                    <option value="accepted" {{ $estado == 'accepted' ? 'selected' : '' }}>Aceptado</option>
                                    <option value="preparing" {{ $estado == 'preparing' ? 'selected' : '' }}>Preparando</option>
                                    <option value="ready" {{ $estado == 'ready' ? 'selected' : '' }}>Listo</option>
                                    <option value="delivered" {{ $estado == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                    <option value="rejected" {{ $estado == 'rejected' ? 'selected' : '' }}>Rechazado</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Acciones rápidas - Versión simplificada -->
        <div class="acciones-rapidas">
            <form action="{{ route('vendedor.pedidoitems.actualizar.todos', $pedido->id) }}" method="POST">
                @csrf
                <input type="hidden" name="estado" value="preparing">
                <button type="submit" class="btn-accion">
                    <i class="fas fa-clock"></i> Marcar todos en preparación
                </button>
            </form>

            <form action="{{ route('vendedor.pedidoitems.actualizar.todos', $pedido->id) }}" method="POST">
                @csrf
                <input type="hidden" name="estado" value="ready">
                <button type="submit" class="btn-accion">
                    <i class="fas fa-check"></i> Marcar todos listos
                </button>
            </form>

            <button type="button" class="btn-vino" onclick="actualizarEstadoPedido()">
                <i class="fas fa-sync-alt"></i> Actualizar estado del pedido
            </button>
        </div>
    </div>
</div>

<script>
    function asignarRepartidor() {
        const select = document.getElementById('repartidor-select');
        const repartidorId = select.value;

        if (!repartidorId) {
            alert('Por favor selecciona un repartidor');
            return;
        }

        // Simular envío de datos al servidor
        alert('Repartidor asignado correctamente');
        // Aquí iría la lógica real para asignar el repartidor
    }

    function actualizarEstadoPedido() {
        // Simular actualización del estado del pedido
        alert('Estado del pedido actualizado correctamente');
        // Aquí iría la lógica real para actualizar el estado del pedido
    }

    // Mostrar loading al enviar formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const buttons = this.querySelectorAll('button');
            buttons.forEach(button => {
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                button.disabled = true;
            });
        });
    });
</script>
</body>
</html>
