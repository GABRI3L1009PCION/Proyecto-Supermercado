<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class AdminCategoriaController extends Controller
{
    public function index()
    {
        $q      = request('q');
        $estado = request('estado');

        $categorias = \App\Models\Categoria::query()
            ->when($q, function ($qbuilder) use ($q) {
                $qbuilder->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->when($estado, fn ($qb) => $qb->where('estado', $estado))
            ->orderByDesc('id')
            ->paginate(15)                // ← importante
            ->withQueryString();          // conserva ?q&estado en los links

        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:120',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|in:activo,inactivo',
        ]);

        Categoria::create($request->only('nombre','descripcion','estado'));

        return redirect()
            ->route('admin.categorias.index')   // 👈 corregido
            ->with('ok','Categoría creada correctamente.');
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre'      => 'required|string|max:120',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|in:activo,inactivo',
        ]);

        $categoria->update($request->only('nombre','descripcion','estado'));

        return redirect()
            ->route('admin.categorias.index')   // 👈 corregido
            ->with('ok','Categoría actualizada.');
    }

    public function destroy($id)
    {
        Categoria::destroy($id);

        return redirect()
            ->route('admin.categorias.index')   // 👈 corregido
            ->with('ok','Categoría eliminada.');
    }
}
