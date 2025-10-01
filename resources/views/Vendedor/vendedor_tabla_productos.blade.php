<div class="table-responsive">
    <table>
        <thead>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $p)
            <tr>
                <td>{{ $p->nombre }}</td>
                <td>Q{{ number_format($p->precio,2) }}</td>
                <td>{{ $p->stock }}</td>
                <td>
                    @php $s = $p->status; @endphp
                    <span class="badge
            {{ $s==='approved' ? 'badge-success'
              : ($s==='pending' ? 'badge-warning'
              : ($s==='rejected' ? 'badge-danger' : 'badge-info')) }}">
            {{ ucfirst($s) }}
          </span>
                </td>
                <td style="display:flex; gap:.25rem; flex-wrap:wrap;">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary btn-edit"
                        data-update-url="{{ route('vendedor.productos.update', $p) }}"
                        data-nombre="{{ e($p->nombre) }}"
                        data-descripcion="{{ e($p->descripcion ?? '') }}"
                        data-precio="{{ $p->precio }}"
                        data-stock="{{ $p->stock }}"
                        data-categoria-id="{{ $p->categoria_id }}"
                    >Editar</button>

                    @if($s === 'approved')
                        <span class="btn btn-sm btn-warning" title="Ocultar temporalmente">Bajar</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Sin productos en esta pestaña.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>


