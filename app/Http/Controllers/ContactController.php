<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // Datos básicos del negocio (ajusta a tu realidad)
        $business = [
            'brand' => 'Atlantia Supermarket',
            'phone' => '+502 5535 5469',
            'whatsapp' => '50255551234', // solo dígitos para wa.me
            'email' => 'soporte@atlantia.gt',
            'address' => 'Puerto Barrios, Izabal, Guatemala',
            'hours' => ['Lun–Sáb: 8:00–20:00', 'Dom: 9:00–18:00'],
            'map_query' => 'Atlantia Supermarket, Puerto Barrios',
        ];

        return view('contact.index', compact('business'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required','string','max:120'],
            'email'        => ['required','email','max:150'],
            'phone'        => ['nullable','string','max:30'],
            'type'         => ['required','in:soporte,facturacion,devoluciones,proveedor,empleo,mayorista,sugerencia'],
            'subject'      => ['nullable','string','max:180'],
            'order_number' => ['nullable','string','max:60'],
            'message'      => ['required','string','max:5000'],
            'attachment'   => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp','max:4096'],
            'consent'      => ['accepted'],
        ], [
            'consent.accepted' => 'Debes aceptar la política de privacidad.',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')
                ->store('contact_attachments', 'public');
        }

        ContactMessage::create($data);

        return redirect()->route('contact.index')
            ->with('success', '¡Gracias! Hemos recibido tu mensaje. Respondemos en 24–48 h hábiles.');
    }
}
