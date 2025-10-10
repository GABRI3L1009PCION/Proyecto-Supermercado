<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryZoneController extends Controller
{
    /**
     * Mostrar listado de zonas de entrega.
     */
    public function index()
    {
        $zones = DeliveryZone::orderBy('municipio')
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.delivery_zones.index', [
            'zones'      => $zones,
            'municipios' => DeliveryZone::municipiosDisponibles(),
        ]);
    }

    /**
     * Mostrar formulario para crear nueva zona.
     */
    public function create()
    {
        return view('admin.delivery_zones.create', [
            'municipios' => DeliveryZone::municipiosDisponibles(),
        ]);
    }

    /**
     * Guardar una nueva zona en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DeliveryZone::create($data);

        return redirect()
            ->route('admin.delivery-zones.index')
            ->with('success', 'Zona de entrega creada correctamente.');
    }

    /**
     * Mostrar formulario para editar una zona existente.
     */
    public function edit(DeliveryZone $deliveryZone)
    {
        return view('admin.delivery_zones.edit', [
            'zone'       => $deliveryZone,
            'municipios' => DeliveryZone::municipiosDisponibles(),
        ]);
    }

    /**
     * Actualizar una zona de entrega existente.
     */
    public function update(Request $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $data = $this->validateData($request, $deliveryZone->id);

        $deliveryZone->update($data);

        return redirect()
            ->route('admin.delivery-zones.index')
            ->with('success', 'Zona de entrega actualizada correctamente.');
    }

    /**
     * Eliminar una zona de entrega de la base de datos.
     */
    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        $deliveryZone->delete();

        return redirect()
            ->route('admin.delivery-zones.index')
            ->with('success', 'Zona eliminada correctamente.');
    }

    /**
     * Validar los datos de entrada del formulario.
     */
    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        $municipios = DeliveryZone::municipiosDisponibles();

        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:150'],
            'municipio'   => ['required', 'string', Rule::in($municipios)],
            'lat'         => ['nullable', 'numeric', 'between:-90,90'],
            'lng'         => ['nullable', 'numeric', 'between:-180,180'],
            'tarifa_base' => ['required', 'numeric', 'min:0', 'max:500'],
            'activo'      => ['nullable', 'boolean'],
        ]);

        // Si el checkbox no viene marcado, se interpreta como falso
        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }
}
