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
        $vendorId = $this->vendorId();

        $zones = VendorDeliveryZone::where('vendor_id', $vendorId)
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->paginate(10);

        return view('Vendedor.zonas.index', [
            'zones' => $zones,
        ]);
    }

    public function create()
    {
        $this->vendorId();

        return view('Vendedor.zonas.form', [
            'zone' => new VendorDeliveryZone(),
            'action' => route('vendedor.zonas.store'),
            'method' => 'POST',
            'title' => 'Registrar zona de reparto',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $vendorId = $this->vendorId();
        $data = $this->validateData($request);
        $data['vendor_id'] = $vendorId;

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
            'nombre'      => ['required', 'string', 'max:120'],
            'coverage'    => ['nullable', 'string', 'max:500'],
            'delivery_fee'=> ['required', 'numeric', 'min:0', 'max:500'],
            'activo'      => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'Ingresa un nombre para la zona.',
            'delivery_fee.required' => 'Define la tarifa para esta zona.',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }

    protected function vendorId(): int
    {
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        return (int) $vendorId;
    }

    protected function authorizeZone(VendorDeliveryZone $zone): void
    {
        $vendorId = $this->vendorId();
        abort_if((int) $zone->vendor_id !== $vendorId, 403, 'No puedes gestionar esta zona.');
    }
}
