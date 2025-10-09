@extends('layouts.app')

@section('content')
    @include('admin.delivery_zones.partials.form', [
        'title' => 'Registrar zona de entrega',
        'action' => route('admin.delivery-zones.store'),
        'method' => 'POST',
        'zone' => new \App\Models\DeliveryZone(),
    ])
@endsection
