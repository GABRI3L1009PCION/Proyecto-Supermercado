<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos | Supermercado Atlantia</title>
    <link rel="icon" href="img/LogoAtlan.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --vino: #800020;
            --vino-oscuro: #5a0017;
            --gris: #f7f7f7;
            --blanco: #fff;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: var(--gris);
            padding: 2rem;
        }

        h2 {
            color: var(--vino);
            text-align: center;
            margin-bottom: 2rem;
        }

        .btn-crear {
            display: inline-block;
            background: var(--vino);
            color: var(--blanco);
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .producto-card {
            background: var(--blanco);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .producto-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #ddd;
        }

        .producto-info {
            padding: 1rem;
            flex-grow: 1;
        }

        .producto-info h3 {
            margin: 0 0 0.5rem 0;
            color: var(--vino);
        }

        .producto-info p {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .producto-info span {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .acciones {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem;
            background: #f1f1f1;
        }

        .acciones a, .acciones button {
            border: none;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .acciones a.edit {
            background: #0066cc;
        }

        .acciones a.edit:hover {
            background: #004999;
        }

        .acciones button.delete {
            background: #c0392b;
        }

        .acciones button.delete:hover {
            background: #962d22;
        }

        .no-data {
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>

<h2>📦 Lista de Productos</h2>

<a href="{{ route('productos.create') }}" class="btn-crear">+ Crear nuevo producto</a>

@if(count($productos) > 0)
    <div class="cards-container">
        @foreach($productos as $producto)
            <div class="producto-card">
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">

                <div class="producto-info">
                    <h3>{{ $producto->nombre }}</h3>
                    <p>{{ $producto->descripcion }}</p>
                    <span>Precio: Q{{ number_format($producto->precio, 2) }}</span>
                    <span>Stock: {{ $producto->stock }}</span>
                </div>

                <div class="acciones">
                    <a href="{{ route('productos.edit', $producto) }}" class="edit">Editar</a>

                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">Eliminar</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="no-data">No hay productos disponibles.</div>
@endif

</body>
</html>
