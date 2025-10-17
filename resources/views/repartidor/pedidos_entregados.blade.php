@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center text-success">📦 Pedidos Entregados</h2>
        @if($pedidos->count())
            <ul class="list-group mt-4">
                @foreach ($pedidos as $pedido)
                    <li class="list-group-item">
                        Pedido {{ $pedido->codigo ?? ('PED-' . $pedido->id) }} - {{ optional($pedido->cliente)->name ?? 'Cliente' }}
                        <span class="float-end">Entregado el {{ optional($pedido->fecha_entregado)->format('d/m/Y H:i') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-secondary mt-4">No has entregado pedidos aún.</div>
        @endif
    </div>
@endsection
