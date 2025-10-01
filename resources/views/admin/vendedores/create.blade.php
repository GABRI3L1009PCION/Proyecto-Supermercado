@extends('layouts.app')

@section('content')
    <div style="display:flex;gap:16px;flex-direction:column;padding:16px">
        @if(session('ok'))
            <div style="background:#e6fffa;border:1px solid #99f6e4;padding:10px;border-radius:6px">{{ session('ok') }}</div>
        @endif

        @if ($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;padding:10px;border-radius:6px">
                <strong>Revisa los campos:</strong>
                <ul style="margin:6px 0 0 18px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:flex;justify-content:space-between;align-items:center">
            <h2 style="margin:0">Crear / Promover Vendedor</h2>
            <a href="{{ route('admin.vendedores.index') }}" style="text-decoration:none;background:#1f2937;color:#fff;padding:8px 12px;border-radius:6px">Volver</a>
        </div>

        {{-- PROMOVER USUARIO EXISTENTE --}}
        <div style="background:#fff;padding:16px;border:1px solid #ddd;border-radius:8px">
            <h3 style="margin-top:0">Promover usuario existente</h3>
            <form method="POST" action="{{ route('admin.vendedores.store') }}">
                @csrf
                <input type="hidden" name="mode" value="existing">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label>Usuario</label>
                        @if(!empty($usuarios) && count($usuarios))
                            <select name="user_id" class="form-control" required>
                                <option value="" disabled selected>Selecciona…</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        @else
                            <input name="user_id" class="form-control" placeholder="ID de usuario" required>
                        @endif
                    </div>
                    <div>
                        <label>Modo de precios</label>
                        <select name="pricing_mode" class="form-control" required>
                            <option value="markup">Markup</option>
                            <option value="commission">Comisión</option>
                        </select>
                    </div>
                    <div>
                        <label>Comisión (%)</label>
                        <input type="number" step="0.01" name="commission_rate" value="10" class="form-control" required>
                    </div>
                    <div>
                        <label>Área de servicio (opcional)</label>
                        <input name="service_area" class="form-control" placeholder="Puerto Barrios, Santo Tomás…">
                    </div>
                </div>

                <button style="margin-top:12px;background:#0ea5e9;color:#fff;padding:10px 14px;border:none;border-radius:6px;cursor:pointer">
                    Promover a Vendedor
                </button>
            </form>
        </div>

        {{-- CREAR USUARIO NUEVO COMO VENDEDOR --}}
        <div style="background:#fff;padding:16px;border:1px solid #ddd;border-radius:8px">
            <h3 style="margin-top:0">Crear usuario nuevo</h3>
            <form method="POST" action="{{ route('admin.vendedores.store') }}">
                @csrf
                <input type="hidden" name="mode" value="new">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div><label>Nombre</label><input name="name" class="form-control" required></div>
                    <div><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div><label>Contraseña</label><input type="password" name="password" class="form-control" required></div>
                    <div>
                        <label>Modo de precios</label>
                        <select name="pricing_mode" class="form-control" required>
                            <option value="markup">Markup</option>
                            <option value="commission">Comisión</option>
                        </select>
                    </div>
                    <div><label>Comisión (%)</label><input type="number" step="0.01" name="commission_rate" value="10" class="form-control" required></div>
                    <div><label>Área de servicio (opcional)</label><input name="service_area" class="form-control"></div>
                </div>

                <button style="margin-top:12px;background:#16a34a;color:#fff;padding:10px 14px;border:none;border-radius:6px;cursor:pointer">
                    Crear Vendedor
                </button>
            </form>
        </div>
    </div>
@endsection

