@extends('layouts.app')

@section('content')
    <div class="user-wrap">
        <div class="user-header">
            <h1>Usuarios</h1>
            <div class="user-actions">
                <a href="{{ url()->previous() }}" class="btn btn-back">⬅ Regresar</a>
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-create">+ Nuevo usuario</a>
            </div>
        </div>

        <div class="user-table-box">
            <table class="user-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>#{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge role-{{ $u->role }}">{{ $u->role ?? '—' }}</span></td>
                        <td>
                        <span class="badge {{ $u->estado === 'activo' ? 'badge-success' : 'badge-danger' }}">
                            {{ $u->estado ?? '—' }}
                        </span>
                        </td>
                        <td>{{ $u->created_at?->format('Y-m-d') }}</td>
                        <td class="td-actions">
                            <a href="{{ route('admin.usuarios.edit', $u->id) }}" class="btn btn-edit">✏️ Editar</a>
                            <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($users->isEmpty())
                    <tr>
                        <td colspan="7" class="empty">No hay usuarios.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>

    {{-- CSS --}}
    <style>
        /* Wrapper */
        .user-wrap{
            max-width:1100px;
            margin:0 auto;
            padding:20px;
            color:#1f2937;
            font-family:'Segoe UI', Tahoma, sans-serif;
        }

        /* Header */
        .user-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:16px;
        }
        .user-header h1{
            margin:0;
            font-size:26px;
            font-weight:700;
        }
        .user-actions{
            display:flex;
            gap:10px;
        }

        /* Buttons */
        .btn{
            padding:8px 14px;
            border-radius:8px;
            font-size:14px;
            font-weight:600;
            text-decoration:none;
            transition:all .2s ease;
            display:inline-block;
        }
        .btn:hover{ transform:translateY(-2px); }
        .btn-back{ background:#6b7280; color:#fff; }
        .btn-back:hover{ background:#4b5563; }
        .btn-create{ background:#16a34a; color:#fff; }
        .btn-create:hover{ background:#15803d; }
        .btn-edit{ background:#3b82f6; color:#fff; margin-right:6px; }
        .btn-edit:hover{ background:#2563eb; }
        .btn-delete{ background:#dc2626; color:#fff; border:none; cursor:pointer; }
        .btn-delete:hover{ background:#b91c1c; }

        /* Table */
        .user-table-box{
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 2px 6px rgba(0,0,0,.05);
        }
        .user-table{
            width:100%;
            border-collapse:collapse;
            font-size:14px;
        }
        .user-table thead{
            background:#f9fafb;
        }
        .user-table th,
        .user-table td{
            padding:12px;
            text-align:left;
            border-bottom:1px solid #f0f0f0;
        }
        .user-table tbody tr:hover{
            background:#f9fafc;
        }
        .td-actions{
            display:flex;
            gap:6px;
        }

        /* Badges */
        .badge{
            padding:4px 8px;
            border-radius:999px;
            font-size:12px;
            font-weight:600;
        }
        .badge-success{ background:#d1fae5; color:#065f46; }
        .badge-danger { background:#fee2e2; color:#991b1b; }
        .role-admin{ background:#1e3a8a; color:#fff; }
        .role-empleado{ background:#facc15; color:#1e293b; }
        .role-cliente{ background:#93c5fd; color:#1e3a8a; }
        .role-repartidor{ background:#fcd34d; color:#78350f; }
        .role-vendedor{ background:#c084fc; color:#4c1d95; }

        /* Empty */
        .empty{
            text-align:center;
            padding:16px;
            color:#6b7280;
        }

        /* Pagination */
        .pagination{
            margin-top:14px;
        }
    </style>
@endsection
