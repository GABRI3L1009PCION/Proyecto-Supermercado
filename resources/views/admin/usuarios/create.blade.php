@extends('layouts.app')

@section('content')
    <div style="padding:20px;max-width:880px;margin:0 auto">
        <h1 style="margin-bottom:14px">Crear usuario</h1>

        @if ($errors->any())
            <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:12px">
                <ul style="margin:0;padding-left:18px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.usuarios.store') }}" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>Nombre</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="inp">
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="inp">
                </div>

                <div>
                    <label>Teléfono (opcional)</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" class="inp">
                </div>

                <div>
                    <label>Estado</label>
                    <select name="estado" class="inp">
                        <option value="activo" {{ old('estado','activo')==='activo'?'selected':'' }}>Activo</option>
                        <option value="inactivo" {{ old('estado')==='inactivo'?'selected':'' }}>Inactivo</option>
                    </select>
                </div>

                <div>
                    <label>Rol</label>
                    <select name="role" id="role" class="inp" required>
                        <option value="cliente"   {{ old('role')==='cliente'?'selected':'' }}>Cliente</option>
                        <option value="empleado"  {{ old('role')==='empleado'?'selected':'' }}>Empleado</option>
                        <option value="repartidor"{{ old('role')==='repartidor'?'selected':'' }}>Repartidor</option>
                        <option value="vendedor"  {{ old('role')==='vendedor'?'selected':'' }}>Vendedor</option>
                        <option value="admin"     {{ old('role')==='admin'?'selected':'' }}>Administrador</option>
                    </select>
                </div>

                <div>
                    <label>Contraseña</label>
                    <input type="password" name="password" required class="inp">
                </div>
                <div>
                    <label>Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required class="inp">
                </div>
            </div>

            {{-- Campos de vendedor --}}
            <div id="vendorBox" style="margin-top:14px;display:none;border-top:1px dashed #e5e7eb;padding-top:14px">
                <h3 style="margin:0 0 10px 0">Datos del vendedor</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label>Zona/Área de servicio</label>
                        <input type="text" name="v_service_area" value="{{ old('v_service_area') }}" class="inp">
                    </div>
                    <div>
                        <label>Modo de cobro</label>
                        <select name="v_pricing_mode" class="inp">
                            <option value="markup" {{ old('v_pricing_mode','markup')==='markup'?'selected':'' }}>Markup</option>
                            <option value="commission" {{ old('v_pricing_mode')==='commission'?'selected':'' }}>Commission</option>
                        </select>
                    </div>
                    <div>
                        <label>% Comisión (si aplica)</label>
                        <input type="number" step="0.01" name="v_commission_rate" value="{{ old('v_commission_rate') }}" class="inp" placeholder="0 - 100">
                    </div>
                    <div style="grid-column:1/-1">
                        <label>Información bancaria (opcional)</label>
                        <textarea name="v_bank" class="inp" rows="3">{{ old('v_bank') }}</textarea>
                    </div>
                </div>
            </div>

            <div style="margin-top:16px">
                <button class="btn" style="background:#16a34a;color:#fff;padding:10px 14px;border-radius:10px;border:0">Crear usuario</button>
                <a href="{{ route('admin.usuarios.index') }}" style="margin-left:8px;text-decoration:none">Cancelar</a>
            </div>
        </form>
    </div>

    {{-- mini estilos inputs --}}
    <style>
        .inp{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:10px}
        label{display:block;font-size:13px;color:#374151;margin-bottom:6px}
    </style>

    <script>
        const roleSel = document.getElementById('role');
        const box = document.getElementById('vendorBox');
        function toggleVendor(){ box.style.display = roleSel.value === 'vendedor' ? 'block' : 'none'; }
        roleSel.addEventListener('change', toggleVendor);
        toggleVendor(); // al cargar
    </script>
@endsection

