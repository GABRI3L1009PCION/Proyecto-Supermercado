<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function estado($id)
    {
        return view('vendedor.items.estado', compact('id'));
    }
}
