@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center text-dark">📍 Rastreo de Pedido</h2>
        <div class="mt-4">
            <form method="GET" action="{{ route('repartidor.rastreo') }}">
                <div class="input-group">
                    <input type="text" name="codigo" class="form-control" placeholder="Ingrese el ID del pedido">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>

            @if(isset($pedido))
                <div class="card mt-4">
                    <div class="card-body">
                        <h5>Pedido #{{ $pedido->id }}</h5>
                        <p>Estado actual: <strong>{{ $pedido->estado }}</strong></p>
                        <p>Dirección de entrega: {{ $pedido->direccion_entrega }}</p>
                    </div>
                </div>
            @elseif(request()->has('codigo'))
                <div class="alert alert-danger mt-4">Pedido no encontrado.</div>
            @endif
        </div>
    </div>
@endsection
