@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center text-success">📦 Pedidos Entregados</h2>
        @if($pedidos->count())
            <ul class="list-group mt-4">
                @foreach ($pedidos as $pedido)
                    <li class="list-group-item">
                        Pedido #{{ $pedido->id }} - {{ $pedido->cliente->nombre }}
                        <span class="float-end">Entregado el {{ $pedido->fecha_entrega }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-secondary mt-4">No has entregado pedidos aún.</div>
        @endif
    </div>
@endsection
