<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos | Supermercado Atlantia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --vino: #800020;
            --vino-hover: #a1002c;
            --gris-claro: #f4f4f4;
            --borde: #e0e0e0;
            --texto: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: var(--gris-claro);
            padding: 1rem;
            color: var(--texto);
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        .header-movil {
            display: none;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }

        h2 {
            color: var(--vino);
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .btn {
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-create {
            background: var(--vino);
            color: white;
        }

        .btn-create:hover {
            background: var(--vino-hover);
            transform: translateY(-2px);
        }

        .btn-back {
            background: #444;
            color: white;
            margin-left: 0.5rem;
        }

        .btn-back:hover {
            background: #222;
            transform: translateY(-2px);
        }

        .botones-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        thead {
            background: #f7f7f7;
            color: var(--texto);
        }

        th, td {
            text-align: left;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid var(--borde);
            vertical-align: middle;
        }

        img.thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #eee;
        }

        .acciones {
            display: flex;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }

        .acciones a, .acciones button {
            padding: 0.5rem 0.8rem;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: bold;
            color: white;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .acciones a.edit {
            background-color: #007bff;
        }

        .acciones a.edit:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .acciones button.delete {
            background-color: #dc3545;
        }

        .acciones button.delete:hover {
            background-color: #a71d2a;
            transform: translateY(-1px);
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #777;
            font-style: italic;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .container {
                padding: 1rem;
                border-radius: 8px;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 1rem;
            }

            .botones-container {
                width: 100%;
                justify-content: space-between;
            }

            .header-movil {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid var(--borde);
            }

            .header-desktop {
                display: none;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            th, td {
                padding: 0.7rem 0.5rem;
                font-size: 0.9rem;
            }

            .acciones {
                flex-direction: column;
                gap: 0.3rem;
            }

            .acciones a, .acciones button {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
                justify-content: center;
            }
        }

        @media screen and (max-width: 576px) {
            .botones-container {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
                margin: 0.2rem 0;
            }

            .btn-back {
                margin-left: 0;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid var(--borde);
                border-radius: 8px;
                padding: 0.5rem;
                background: #f9f9f9;
            }

            td {
                padding: 0.5rem;
                position: relative;
                padding-left: 45%;
                border-bottom: 1px solid #eee;
            }

            td:last-child {
                border-bottom: none;
                padding-left: 0.5rem;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                position: absolute;
                left: 0.5rem;
                width: 40%;
                padding-right: 0.5rem;
                color: var(--vino);
            }

            .acciones {
                flex-direction: row;
                justify-content: flex-start;
                padding-left: 0;
                margin-top: 0.5rem;
            }

            img.thumb {
                width: 50px;
                height: 50px;
            }

            /* Ocultar el label para la columna de acciones en móviles */
            td[data-label="Acciones"]::before {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-movil">
        <h2>📋 Productos</h2>
        <a href="{{ route('admin.panel') }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <div class="top-bar">
        <div class="botones-container">
            <a href="{{ route('admin.productos.create') }}" class="btn btn-create">
                <i class="fas fa-plus"></i> Nuevo producto
            </a>
            <a href="{{ route('admin.panel') }}" class="btn btn-back header-desktop">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </a>
        </div>
        <h2 class="header-desktop">📋 Lista de Productos</h2>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-label="ID">1</td>
            <td data-label="Imagen">
                <img src="https://cdn.pixabay.com/photo/2017/07/05/15/41/milk-2474993_640.jpg" alt="Lactosa Free Leche Deslactosada" class="thumb">
            </td>
            <td data-label="Nombre">Lactosa Free Leche Deslactosada – 1 L</td>
            <td data-label="Descripción">Leche deslactosada para fácil digestión</td>
            <td data-label="Precio">Q20.50</td>
            <td data-label="Stock">12</td>
            <td data-label="Acciones" class="acciones">
                <a href="#" class="edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="#" method="POST" style="display:inline;">
                    <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <tr>
            <td data-label="ID">2</td>
            <td data-label="Imagen">
                <img src="https://cdn.pixabay.com/photo/2018/01/04/17/47/milk-3061490_640.jpg" alt="Dos Pinos Leche Entera" class="thumb">
            </td>
            <td data-label="Nombre">Dos Pinos "Pinito" Leche Entera UHT – 1 L</td>
            <td data-label="Descripción">Leche pasteurizada en envase Tetra Pak; sabor tradicional</td>
            <td data-label="Precio">Q18.50</td>
            <td data-label="Stock">15</td>
            <td data-label="Acciones" class="acciones">
                <a href="#" class="edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="#" method="POST" style="display:inline;">
                    <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <tr>
            <td data-label="ID">3</td>
            <td data-label="Imagen">
                <img src="https://cdn.pixabay.com/photo/2017/09/25/18/23/milk-bottle-2786294_640.jpg" alt="Australian Pride Leche" class="thumb">
            </td>
            <td data-label="Nombre">Australian Pride Leche Deslactosada Semidescremada – 1 L</td>
            <td data-label="Descripción">Ideal para quienes buscan menor contenido de lactosa</td>
            <td data-label="Precio">Q19.00</td>
            <td data-label="Stock">8</td>
            <td data-label="Acciones" class="acciones">
                <a href="#" class="edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="#" method="POST" style="display:inline;">
                    <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <tr>
            <td data-label="ID">4</td>
            <td data-label="Imagen">
                <img src="https://cdn.pixabay.com/photo/2016/08/11/08/49/yogurt-1586379_640.jpg" alt="Yogurt Natural Griego" class="thumb">
            </td>
            <td data-label="Nombre">Yogurt Natural Griego – 500 g</td>
            <td data-label="Descripción">Yogurt cremoso alto en proteína</td>
            <td data-label="Precio">Q25.75</td>
            <td data-label="Stock">20</td>
            <td data-label="Acciones" class="acciones">
                <a href="#" class="edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="#" method="POST" style="display:inline;">
                    <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <tr>
            <td data-label="ID">5</td>
            <td data-label="Imagen">
                <img src="https://cdn.pixabay.com/photo/2018/04/13/17/09/cheese-3316963_640.jpg" alt="Queso Gouda" class="thumb">
            </td>
            <td data-label="Nombre">Queso Gouda en Rebanadas – 200 g</td>
            <td data-label="Descripción">Queso suave y cremoso, ideal para sandwiches</td>
            <td data-label="Precio">Q22.00</td>
            <td data-label="Stock">10</td>
            <td data-label="Acciones" class="acciones">
                <a href="#" class="edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="#" method="POST" style="display:inline;">
                    <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <tr>
            <td data-label="ID">6</td>
            <td data-label="Imagen">
                <img src="https://cdn.pixabay.com/photo/2018/05/15/10/44/butter-3402522_640.jpg" alt="Mantequilla" class="thumb">
            </td>
            <td data-label="Nombre">Mantequilla con Sal – 250 g</td>
            <td data-label="Descripción">Mantequilla cremosa para cocinar y untar</td>
            <td data-label="Precio">Q15.50</td>
            <td data-label="Stock">18</td>
            <td data-label="Acciones" class="acciones">
                <a href="#" class="edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="#" method="POST" style="display:inline;">
                    <button type="submit" class="delete" onclick="return confirm('¿Eliminar producto?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mejorar la experiencia en dispositivos táctiles
        const buttons = document.querySelectorAll('.btn, .acciones a, .acciones button');
        buttons.forEach(button => {
            button.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });

            button.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    });
</script>

</body>
</html>
