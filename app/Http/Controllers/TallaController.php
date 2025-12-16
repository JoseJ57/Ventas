<?php

namespace App\Http\Controllers;

use App\Models\Talla;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TallaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tallas = Talla::orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Tallas/Index', [
            'tallas' => $tallas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tallas/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:talla,nombre',
        ], [
            'nombre.required' => 'El nombre de la talla es obligatorio.',
            'nombre.unique' => 'Esta talla ya existe.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
        ]);

        Talla::create($validated);

        return redirect()->route('tallas.index')
            ->with('success', 'Talla creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Talla $talla)
    {
        return Inertia::render('Tallas/Show', [
            'talla' => $talla
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Talla $talla)
    {
        return Inertia::render('Tallas/Edit', [
            'talla' => $talla
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Talla $talla)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:talla,nombre,' . $talla->id,
        ], [
            'nombre.required' => 'El nombre de la talla es obligatorio.',
            'nombre.unique' => 'Esta talla ya existe.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
        ]);

        $talla->update($validated);

        return redirect()->route('tallas.index')
            ->with('success', 'Talla actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Talla $talla)
    {
        try {
            $talla->delete();

            return redirect()->route('tallas.index')
                ->with('success', 'Talla eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('tallas.index')
                ->with('error', 'No se pudo eliminar la talla. Puede estar asociada a otros registros.');
        }
    }
}
