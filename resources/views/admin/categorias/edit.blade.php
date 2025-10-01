@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 40px auto; background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

    <!-- Título -->
    <h2 style="text-align: center; color: #800020; margin-bottom: 30px; font-size: 24px;">
        ✏️ Editar Categoría
    </h2>

    <form method="POST" action="{{ route('admin.categorias.update', $categoria->id) }}">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div style="margin-bottom: 20px;">
            <label for="nombre" style="display: block; font-weight: bold; margin-bottom: 6px; color: #800020;">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="{{ $categoria->nombre }}" required style="
                    width: 100%;
                    padding: 12px;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    font-size: 16px;
                ">
        </div>

        <!-- Botones -->
        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px;">

            <button type="submit" style="
                    background-color: #28a745;
                    color: #fff;
                    padding: 12px 25px;
                    border: none;
                    border-radius: 6px;
                    font-size: 15px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                " onmouseover="this.style.backgroundColor='#218838'" onmouseout="this.style.backgroundColor='#28a745'">
                ✅ Actualizar categoría
            </button>

            <a href="{{ route('admin.categorias.index') }}" style="
                    background-color: #800020;
                    color: #fff;
                    padding: 12px 25px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-size: 15px;
                    font-weight: bold;
                    display: inline-block;
                    transition: background-color 0.3s ease;
                " onmouseover="this.style.backgroundColor='#5a0017'" onmouseout="this.style.backgroundColor='#800020'">
                ← Regresar
            </a>
        </div>
    </form>
</div>
@endsection