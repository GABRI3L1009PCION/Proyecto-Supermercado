@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center text-success">✅ Confirmar Entrega</h2>
        <div class="mt-4">
            <form method="POST" action="{{ route('repartidor.confirmarEntrega') }}">
                @csrf
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="text" name="pedido_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="observacion" class="form-label">Observaciones (opcional)</label>
                    <textarea name="observacion" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Confirmar Entrega</button>
            </form>
        </div>
    </div>
@endsection
