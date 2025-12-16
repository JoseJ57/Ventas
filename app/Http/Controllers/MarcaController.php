<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $marcas = Marca::query()
            ->when($search, function ($query, $search) {
                $query->search($search);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Marcas/Index', [
            'marcas' => $marcas,
            'filters' => [
                'search' => $search
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Marcas/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:30|unique:marcas,nombre',
            'empresa' => 'required|string|max:30',
            'pais' => 'nullable|string|max:50',
        ], [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.unique' => 'Esta marca ya existe.',
            'nombre.max' => 'El nombre no puede exceder 30 caracteres.',
            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'empresa.max' => 'El nombre de la empresa no puede exceder 30 caracteres.',
            'pais.max' => 'El país no puede exceder 50 caracteres.',
        ]);

        Marca::create($validated);

        return redirect()->route('marcas.index')
            ->with('success', 'Marca creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        return Inertia::render('Marcas/Show', [
            'marca' => $marca
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        return Inertia::render('Marcas/Edit', [
            'marca' => $marca
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:30|unique:marcas,nombre,' . $marca->id,
            'empresa' => 'required|string|max:30',
            'pais' => 'nullable|string|max:50',
        ], [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.unique' => 'Esta marca ya existe.',
            'nombre.max' => 'El nombre no puede exceder 30 caracteres.',
            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'empresa.max' => 'El nombre de la empresa no puede exceder 30 caracteres.',
            'pais.max' => 'El país no puede exceder 50 caracteres.',
        ]);

        $marca->update($validated);

        return redirect()->route('marcas.index')
            ->with('success', 'Marca actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        try {
            $marca->delete();

            return redirect()->route('marcas.index')
                ->with('success', 'Marca eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('marcas.index')
                ->with('error', 'No se pudo eliminar la marca. Puede estar asociada a otros registros.');
        }
    }
}
