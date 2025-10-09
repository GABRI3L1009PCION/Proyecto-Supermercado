@extends('layouts.app')

@section('content')
    @include('admin.delivery_zones.partials.form', [
        'title' => 'Editar zona de entrega',
        'action' => route('admin.delivery-zones.update', $zone),
        'method' => 'PUT',
        'zone' => $zone,
    ])
@endsection
