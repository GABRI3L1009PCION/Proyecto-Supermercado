@php
    $zone = $zone ?? new \App\Models\DeliveryZone();
    $municipios = $municipios ?? \App\Models\DeliveryZone::municipiosDisponibles();
@endphp

<style>
    .zone-form-card{max-width:720px;margin:0 auto;background:#fff;border-radius:16px;padding:24px;border:1px solid #e5e7eb;box-s
hadow:0 6px 16px rgba(15,23,42,0.08)}
    .zone-form-card h1{font-size:26px;font-weight:800;color:#1f2937;margin-bottom:16px}
    .zone-form-wrapper{padding:24px;background:#f5f6f8;min-height:100vh}
    .form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}
    label{display:flex;flex-direction:column;font-weight:600;color:#1f2937;font-size:14px}
    input,select{margin-top:6px;padding:12px 14px;border-radius:10px;border:1px solid #d1d5db;font-size:15px}
    input:focus,select:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,0.1)}
    .form-actions{margin-top:24px;display:flex;gap:12px;flex-wrap:wrap}
    .btn{padding:12px 20px;border-radius:10px;font-weight:600;border:none;cursor:pointer}
    .btn-primary{background:#2563eb;color:#fff}
    .btn-secondary{background:#fff;color:#1f2937;border:1px solid #d1d5db;text-decoration:none}
    .switch{display:flex;align-items:center;gap:8px;margin-top:18px}
    .error-text{color:#b91c1c;font-size:12px;font-weight:500;margin-top:6px}
    .alert{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-weight:600}
    .alert-error{background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5}
</style>

<div class="zone-form-wrapper">
    <div class="zone-form-card">
        <h1>{{ $title ?? 'Zona de entrega' }}</h1>

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}">
            @csrf
            @if(($method ?? 'POST') !== 'POST')
                @method($method)
            @endif

            <div class="form-grid">
                <label>
                    <span>Nombre de la zona</span>
                    <input type="text" name="nombre" value="{{ old('nombre', $zone->nombre) }}" required>
                    @error('nombre')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    <span>Municipio</span>
                    <select name="municipio" required>
                        <option value="">Seleccione</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio }}" @selected(old('municipio', $zone->municipio) === $municipio)>
                                {{ $municipio }}
                            </option>
                        @endforeach
                    </select>
                    @error('municipio')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    <span>Tarifa base (Q)</span>
                    <input type="number" step="0.01" min="0" max="500" name="tarifa_base" value="{{ old('tarifa_base', $zone
                        ->tarifa_base ?? 0) }}" required>
                    @error('tarifa_base')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    <span>Latitud</span>
                    <input type="number" step="0.000001" name="lat" value="{{ old('lat', $zone->lat) }}">
                    @error('lat')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    <span>Longitud</span>
                    <input type="number" step="0.000001" name="lng" value="{{ old('lng', $zone->lng) }}">
                    @error('lng')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="switch">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $zone->activo ?? true))>
                <label for="activo" style="margin:0;font-weight:600;">Zona activa</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
