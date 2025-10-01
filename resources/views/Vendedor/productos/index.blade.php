@extends('layouts.app')

@section('content')
    <div class="v-wrap">
        <div class="v-head">
            <h1>Mis productos</h1>
            <a href="{{ route('vendedor.productos.create') }}" class="v-btn v-btn-primary">+ Nuevo producto</a>
        </div>

        @if(session('ok'))
            <div class="v-alert">{{ session('ok') }}</div>
        @endif

        <div class="v-card">
            <div class="v-table-wrap">
                <table class="v-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th style="width:160px">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($productos as $p)
                        <tr>
                            <td>#{{ $p->id }}</td>
                            <td>
                                @if($p->imagen)
                                    <img src="{{ asset('storage/' . $p->imagen) }}" alt="Imagen" width="60" style="border-radius:8px">
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $p->nombre }}</td>
                            <td>Q{{ number_format($p->precio, 2) }}</td>
                            <td>{{ $p->stock }}</td>
                            <td>{{ optional($p->categoria)->nombre ?? '—' }}</td>
                            <td class="v-actions">
                                <a class="v-link" href="{{ route('vendedor.productos.edit', $p) }}">Editar</a>
                                <form action="{{ route('vendedor.productos.destroy', $p) }}" method="POST" onsubmit="return confirm('¿Eliminar producto?')" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="v-link v-link-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Aún no tienes productos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="v-pag">
                {{ $productos->links() }}
            </div>
        </div>
    </div>

    <style>
        .v-wrap{max-width:1100px;margin:0 auto;padding:18px}
        .v-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
        .v-btn{display:inline-block;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;color:#111;background:#fff}
        .v-btn:hover{background:#f8fafc}
        .v-btn-primary{background:#16a34a;border-color:#16a34a;color:#fff}
        .v-alert{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:10px;border-radius:8px;margin-bottom:10px}
        .v-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px}
        .v-table-wrap{overflow:auto}
        .v-table{width:100%;border-collapse:collapse;min-width:760px}
        .v-table th,.v-table td{padding:10px;border-bottom:1px solid #eee;text-align:left;vertical-align: middle}
        .v-table img{max-height:60px;object-fit:cover;border-radius:8px}
        .v-actions .v-link{margin-right:10px}
        .v-link{color:#2563eb;text-decoration:none}
        .v-link:hover{text-decoration:underline}
        .v-link-danger{color:#b91c1c}
        .v-pag{margin-top:10px}
    </style>
@endsection
