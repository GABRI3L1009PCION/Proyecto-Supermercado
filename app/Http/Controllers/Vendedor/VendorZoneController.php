<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\VendorDeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorZoneController extends Controller
{
    public function index()
    {
        $sellerId = $this->sellerId();

        $zones = VendorDeliveryZone::where('seller_id', $sellerId)
            ->orderByDesc('activa')
            ->orderBy('nombre')
            ->paginate(10);

        return view('Vendedor.zonas.index', [
            'zones' => $zones,
        ]);
    }

    public function create()
    {
        $this->sellerId();

        return view('Vendedor.zonas.form', [
            'zone' => new VendorDeliveryZone(),
            'action' => route('vendedor.zonas.store'),
            'method' => 'POST',
            'title' => 'Registrar zona de reparto',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $sellerId = $this->sellerId();
        $data = $this->validateData($request);
        $data['seller_id'] = $sellerId;

        VendorDeliveryZone::create($data);

        return redirect()->route('vendedor.zonas.index')
            ->with('ok', 'Zona registrada correctamente.');
    }

    public function edit(VendorDeliveryZone $zona)
    {
        $this->authorizeZone($zona);

        return view('Vendedor.zonas.form', [
            'zone' => $zona,
            'action' => route('vendedor.zonas.update', $zona),
            'method' => 'PUT',
            'title' => 'Editar zona de reparto',
        ]);
    }

    public function update(Request $request, VendorDeliveryZone $zona): RedirectResponse
    {
        $this->authorizeZone($zona);
        $data = $this->validateData($request);
        $zona->update($data);

        return redirect()->route('vendedor.zonas.index')
            ->with('ok', 'Zona actualizada correctamente.');
    }

    public function destroy(VendorDeliveryZone $zona): RedirectResponse
    {
        $this->authorizeZone($zona);
        $zona->delete();

        return redirect()->route('vendedor.zonas.index')
            ->with('ok', 'Zona eliminada correctamente.');
    }

    protected function validateData(Request $request): array
    {
        $data = $request->validate([
            'nombre'                => ['required', 'string', 'max:120'],
            'descripcion_cobertura' => ['nullable', 'string', 'max:500'],
            'tarifa_reparto'        => ['required', 'numeric', 'min:0', 'max:500'],
            'activa'                => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'Ingresa un nombre para la zona.',
            'tarifa_reparto.required' => 'Define la tarifa para esta zona.',
        ]);

        $data['activa'] = $request->boolean('activa', true);

        return $data;
    }

    protected function sellerId(): int
    {
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        return (int) $vendorId;
    }

    protected function authorizeZone(VendorDeliveryZone $zone): void
    {
        $sellerId = $this->sellerId();
        abort_if((int) $zone->seller_id !== $sellerId, 403, 'No puedes gestionar esta zona.');
    }
}
