@extends('layouts.app')

@section('title', ($title ?? 'Zona de reparto') . ' | Panel de vendedor')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $title ?? 'Zona de reparto' }}</h1>
        <a href="{{ route('vendedor.zonas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ $action }}" method="POST" class="row g-3">
                @csrf
                @if(($method ?? 'POST') !== 'POST')
                    @method($method)
                @endif

                <div class="col-12">
                    <label class="form-label" for="nombre">Nombre de la zona *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           value="{{ old('nombre', $zone->nombre) }}" maxlength="120" required>
                    @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="coverage">Cobertura (barrios, colonias, sectores)</label>
                    <textarea id="coverage" name="coverage" class="form-control" rows="3" maxlength="500"
                              placeholder="Ej: Zona 1 - Col. Las Lomas, Res. Las Flores, Barrio Centro">{{ old('coverage', $zone->coverage) }}</textarea>
                    <small class="text-muted">Describe dónde ofreces la entrega dentro de esta zona.</small>
                    @error('coverage')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="delivery_fee">Tarifa de reparto (Q) *</label>
                    <input type="number" step="0.01" min="0" max="500" id="delivery_fee" name="delivery_fee" class="form-control"
                           value="{{ old('delivery_fee', $zone->delivery_fee) }}" required>
                    @error('delivery_fee')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo"
                               value="1" {{ old('activo', $zone->activo ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Zona activa</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar zona
                    </button>
                    <a href="{{ route('vendedor.zonas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
