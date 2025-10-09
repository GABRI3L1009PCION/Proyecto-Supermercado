<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::orderBy('municipio')->orderBy('nombre')->paginate(20);

        return view('admin.delivery_zones.index', [
            'zones'      => $zones,
            'municipios' => DeliveryZone::municipiosDisponibles(),
            'newZone'    => new DeliveryZone(['activo' => true]),
        ]);
    }

    public function create()
    {
        return view('admin.delivery_zones.create', [
            'municipios' => DeliveryZone::municipiosDisponibles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DeliveryZone::create($data);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Zona de entrega creada correctamente.');
    }

    public function edit(DeliveryZone $delivery_zone)
    {
        return view('admin.delivery_zones.edit', [
            'zone'       => $delivery_zone,
            'municipios' => DeliveryZone::municipiosDisponibles(),
        ]);
    }

    public function update(Request $request, DeliveryZone $delivery_zone): RedirectResponse
    {
        $data = $this->validateData($request, $delivery_zone->id);

        $delivery_zone->update($data);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Zona de entrega actualizada correctamente.');
    }

    public function destroy(DeliveryZone $delivery_zone): RedirectResponse
    {
        try {
            $deleted = $delivery_zone->delete();

            if (! $deleted) {
                return redirect()->route('admin.delivery-zones.index')
                    ->with('error', 'La zona no pudo eliminarse. Inténtalo nuevamente.');
            }

            return redirect()->route('admin.delivery-zones.index')
                ->with('success', 'Zona eliminada correctamente.');
        } catch (QueryException $exception) {
            return redirect()->route('admin.delivery-zones.index')
                ->with('error', 'No se pudo eliminar la zona porque está siendo utilizada.');
        }
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        $municipios = DeliveryZone::municipiosDisponibles();

        $data = $request->validate([
            'nombre'        => ['required', 'string', 'max:150'],
            'municipio'     => ['required', 'string', Rule::in($municipios)],
            'lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'lng'           => ['nullable', 'numeric', 'between:-180,180'],
            'tarifa_base'   => ['required', 'numeric', 'min:0', 'max:500'],
            'activo'        => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }
}
