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
                    <label class="form-label" for="descripcion_cobertura">Cobertura (barrios, colonias, sectores)</label>
                    <textarea id="descripcion_cobertura" name="descripcion_cobertura" class="form-control" rows="3" maxlength="500"
                              placeholder="Ej: Zona 1 - Col. Las Lomas, Res. Las Flores, Barrio Centro">{{ old('descripcion_cobertura', $zone->descripcion_cobertura) }}</textarea>
                    <small class="text-muted">Describe dónde ofreces la entrega dentro de esta zona.</small>
                    @error('descripcion_cobertura')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="tarifa_reparto">Tarifa de reparto (Q) *</label>
                    <input type="number" step="0.01" min="0" max="500" id="tarifa_reparto" name="tarifa_reparto" class="form-control"
                           value="{{ old('tarifa_reparto', $zone->tarifa_reparto) }}" required>
                    @error('tarifa_reparto')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="activa" name="activa"
                               value="1" {{ old('activa', $zone->activa ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activa">Zona activa</label>
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
