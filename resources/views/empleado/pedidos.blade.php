@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-3xl font-bold mb-6">Pedidos en preparación</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @forelse($pedidos as $pedido)
            <div class="bg-white border rounded-xl shadow p-5 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    Pedido #{{ $pedido->id }} – Estado:
                    <span class="text-indigo-600 capitalize">{{ $pedido->estado }}</span>
                </h3>
                <p class="text-sm text-gray-500 mb-3">Cliente: {{ $pedido->cliente->name }}</p>

                <ul class="mb-3">
                    @foreach($pedido->productos as $item)
                        <li>• {{ $item->producto->nombre }} × {{ $item->cantidad }}</li>
                    @endforeach
                </ul>

                <div class="flex gap-4">
                    @if($pedido->estado === 'pendiente')
                        <form method="POST" action="{{ route('empleado.pedidos.preparar', $pedido) }}">
                            @csrf
                            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                                Marcar como preparando
                            </button>
                        </form>
                    @endif

                    @if($pedido->estado === 'preparando')
                        <form method="POST" action="{{ route('empleado.pedidos.listo', $pedido) }}">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Marcar como listo
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-600">No hay pedidos pendientes ni en preparación.</p>
        @endforelse
    </div>
@endsection

