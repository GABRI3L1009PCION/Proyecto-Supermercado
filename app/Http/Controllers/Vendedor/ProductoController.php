<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        $vendorId  = auth()->user()->vendor->id ?? null;
        $productos = Producto::where('vendor_id', $vendorId)
            ->latest()->paginate(15);

        return view('vendedor.productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('vendedor.productos.form', [
            'producto'   => new Producto(),
            'categorias' => $categorias,
            'mode'       => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string', 'max:2000'],
            'precio'       => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'imagen'       => ['nullable', 'image', 'max:2048'], // imagen opcional y válida
        ]);

        $vendorId = auth()->user()->vendor->id ?? null;

        // Procesar imagen si se envió
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create([
            'nombre'       => $request->nombre,
            'slug'         => Str::slug($request->nombre),
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'categoria_id' => $request->categoria_id,
            'vendor_id'    => $vendorId,
            'estado'       => 'activo',
            'imagen'       => $rutaImagen,
        ]);

        return redirect()->route('vendedor.productos.index')->with('ok', 'Producto creado.');
    }

    public function edit(Producto $producto)
    {
        $this->authorizeProducto($producto);

        $categorias = Categoria::orderBy('nombre')->get();
        return view('vendedor.productos.form', [
            'producto'   => $producto,
            'categorias' => $categorias,
            'mode'       => 'edit',
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $this->authorizeProducto($producto);

        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string', 'max:2000'],
            'precio'       => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'imagen'       => ['nullable', 'image', 'max:2048'],
        ]);

        // Imagen nueva (opcional)
        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen); // borrar anterior
            }
            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update([
            'nombre'       => $request->nombre,
            'slug'         => Str::slug($request->nombre),
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'categoria_id' => $request->categoria_id,
            'imagen'       => $producto->imagen,
        ]);

        return redirect()->route('vendedor.productos.index')->with('ok', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $this->authorizeProducto($producto);

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();
        return back()->with('ok', 'Producto eliminado.');
    }

    private function authorizeProducto(Producto $producto): void
    {
        $vendorId = auth()->user()->vendor->id ?? null;
        abort_if($producto->vendor_id !== $vendorId, 403);
    }
}
