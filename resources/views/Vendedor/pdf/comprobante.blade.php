<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de compra - {{ $pedido->id }}</title>
    <style>
        body{ font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#222; }
        .wrap{ width: 92%; margin: 0 auto; }
        .header{ text-align:center; border-bottom:2px solid #8C3A44; padding-bottom:8px; margin-bottom:10px;}
        .logo{ width:120px; height:60px; object-fit:contain; display:block; margin:0 auto 6px;}
        h1{ margin:4px 0 0; font-size:20px; color:#722F37 }
        .muted{ color:#666 }
        .grid{ display:flex; gap:14px; margin-top:8px }
        .box{ flex:1; border:1px solid #ddd; border-radius:6px; padding:8px 10px; }
        h3{ margin:6px 0; color:#722F37; font-size:13px }
        table{ width:100%; border-collapse:collapse; margin-top:8px }
        th,td{ padding:6px 8px; border-bottom:1px solid #eee; }
        thead th{ background:#F9F2F3; color:#722F37; border-bottom:2px solid #D9A6A6; }
        .right{ text-align:right }
        .totales{ margin-top:8px; width:50%; margin-left:auto }
        .thanks{ margin-top:12px; text-align:center; font-weight:700; color:#8C3A44 }
    </style>
</head>
<body>
<div class="wrap">

    <div class="header">
        @if($logoBase64)
            <img class="logo" src="{{ $logoBase64 }}" alt="logo">
        @endif
        <h1>COMPROBANTE DE COMPRA</h1>
        <div class="muted">{{ $empresa['nombre'] }} — NIT {{ $empresa['nit'] }}</div>
        <div class="muted">{{ $empresa['direccion'] }} · Tel {{ $empresa['telefono'] }} · {{ $empresa['email'] }}</div>
        <div class="muted">Pedido #{{ $pedido->id }} · {{ optional($pedido->created_at)->format('d/m/Y H:i') }}</div>
    </div>

    <div class="grid">
        <div class="box">
            <h3>Cliente</h3>
            <div><strong>{{ optional($pedido->cliente)->name ?? '—' }}</strong></div>
            <div>Tel: {{ optional($pedido->cliente)->telefono ?? '--' }}</div>
            <div>Correo: {{ optional($pedido->cliente)->email ?? '--' }}</div>
        </div>
        <div class="box">
            <h3>Entrega</h3>
            @php
                $dir = $pedido->direccion_envio ?? [];
                $texto = [];
                if (!empty($dir['descripcion'])) $texto[] = $dir['descripcion'];
                if (!empty($dir['colonia'])) $texto[] = $dir['colonia'];
                $dirTexto = $texto ? implode(', ', $texto) : '--';
            @endphp
            <div>{{ $dirTexto }}</div>
            @if(!empty($dir['referencia']))<div>Ref: {{ $dir['referencia'] }}</div>@endif
            @if(!empty($dir['lat']) && !empty($dir['lng']))
                <div>Coordenadas: {{ $dir['lat'] }}, {{ $dir['lng'] }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Producto</th>
            <th class="right">Cant.</th>
            <th class="right">Precio</th>
            <th class="right">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $it)
            @php
                $cant = (int) $it->cantidad;
                $pu   = (float) $it->precio_unitario;
            @endphp
            <tr>
                <td>{{ optional($it->producto)->nombre ?? 'Producto' }}</td>
                <td class="right">{{ $cant }}</td>
                <td class="right">Q {{ number_format($pu,2) }}</td>
                <td class="right">Q {{ number_format($cant*$pu,2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr><td class="right">Subtotal:</td><td class="right">Q {{ number_format($subtotal,2) }}</td></tr>
        <tr><td class="right">IVA (12%):</td><td class="right">Q {{ number_format($iva,2) }}</td></tr>
        <tr><td class="right"><strong>Total pagado:</strong></td><td class="right"><strong>Q {{ number_format($total,2) }}</strong></td></tr>
        <tr><td class="right">Método de pago:</td><td class="right">{{ ucfirst(str_replace('_',' ', $pedido->metodo_pago ?? 'efectivo')) }}</td></tr>
    </table>

    @php
        $fac = $pedido->facturacion ?? [];
    @endphp
    @if(!empty($fac['requiere']))
        <div class="box" style="margin-top:10px;">
            <h3>Datos de facturación</h3>
            <div>NIT: {{ $fac['nit'] ?? 'CF' }}</div>
            <div>Nombre: {{ $fac['nombre'] ?? 'Consumidor Final' }}</div>
            <div>Dirección: {{ $fac['direccion'] ?? '—' }}</div>
        </div>
    @endif

    <div class="thanks">¡Gracias por su compra!</div>
</div>
</body>
</html>
