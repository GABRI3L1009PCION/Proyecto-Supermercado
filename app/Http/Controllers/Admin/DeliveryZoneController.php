<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    // Listar zonas del vendedor autenticado
    public function index()
    {
        $sellerId = auth()->id();

        $zonas = DeliveryZone::where('seller_id', $sellerId)->paginate(10);

        return view('vendedor.zonas.index', compact('zonas'));
    }

    public function create()
    {
        return view('vendedor.zonas.create');
    }

    public function store(Request $request)
    {
        $sellerId = auth()->id();

        $validated = $request->validate([
            'nombre'               => ['required', 'string', 'max:255'],
            'descripcion_cobertura'=> ['nullable', 'string', 'max:255'],
            'tarifa_reparto'       => ['required', 'numeric', 'min:0'],
            'estado'               => ['required', 'in:activa,inactiva'],
        ]);

        $validated['seller_id'] = $sellerId;

        DeliveryZone::create($validated);

        return redirect()
            ->route('vendedor.zonas.index')
            ->with('success', 'Zona de reparto creada correctamente.');
    }

    public function edit(DeliveryZone $zona)
    {
        // Seguridad: solo dueño puede editar
        $this->authorizeZona($zona);

        return view('vendedor.zonas.edit', compact('zona'));
    }

    public function update(Request $request, DeliveryZone $zona)
    {
        $this->authorizeZona($zona);

        $validated = $request->validate([
            'nombre'               => ['required', 'string', 'max:255'],
            'descripcion_cobertura'=> ['nullable', 'string', 'max:255'],
            'tarifa_reparto'       => ['required', 'numeric', 'min:0'],
            'estado'               => ['required', 'in:activa,inactiva'],
        ]);

        $zona->update($validated);

        return redirect()
            ->route('vendedor.zonas.index')
            ->with('success', 'Zona de reparto actualizada correctamente.');
    }

    public function destroy(DeliveryZone $zona)
    {
        $this->authorizeZona($zona);

        $zona->delete();

        return redirect()
            ->route('vendedor.zonas.index')
            ->with('success', 'Zona de reparto eliminada correctamente.');
    }

    protected function authorizeZona(DeliveryZone $zona): void
    {
        abort_if($zona->seller_id !== auth()->id(), 403);
    }
}
