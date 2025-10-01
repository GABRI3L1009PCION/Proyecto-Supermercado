<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body{font-family: DejaVu Sans, sans-serif; font-size:12px}
        .title{font-weight:bold; font-size:16px; margin-bottom:8px}
        table{width:100%; border-collapse:collapse; margin-top:10px}
        th,td{border:1px solid #ccc; padding:6px; text-align:left}
        th{background:#f3f3f3}
    </style>
</head>
<body>
<div class="title">Comprobante / Factura #{{ $pedido->codigo ?? ('PED-'.$pedido->id) }}</div>
<div>Cliente: {{ optional($pedido->cliente)->name }}</div>
<div>Fecha: {{ optional($pedido->created_at)->format('d/m/Y H:i') }}</div>

<table>
    <thead>
    <tr><th>Producto</th><th>Cant</th><th>Precio</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
    @foreach($pedido->items as $it)
        @php($p = (float)($it->precio_unitario ?? 0))
        <tr>
            <td>{{ optional($it->producto)->nombre }}</td>
            <td>{{ $it->cantidad }}</td>
            <td>Q{{ number_format($p,2) }}</td>
            <td>Q{{ number_format($p * (int)$it->cantidad,2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p style="text-align:right; margin-top:8px;">
    <strong>Total: Q{{ number_format((float)$pedido->total,2) }}</strong>
</p>
</body>
</html>

