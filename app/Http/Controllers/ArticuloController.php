<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\TipoArticulo;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');
        $tipo_articulo_id = $request->input('tipo_articulo_id');
        $marca_id = $request->input('marca_id');

        $articulos = Articulo::with(['tipoArticulo', 'marca'])
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('eslogan', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->when($tipo_articulo_id, function ($query, $tipo_articulo_id) {
                $query->where('tipo_articulo_id', $tipo_articulo_id);
            })
            ->when($marca_id, function ($query, $marca_id) {
                $query->where('marca_id', $marca_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('Articulos/Index', [
            'articulos' => $articulos,
            'filters' => [
                'search' => $search,
                'estado' => $estado,
                'tipo_articulo_id' => $tipo_articulo_id,
                'marca_id' => $marca_id,
            ],
            'tipos' => TipoArticulo::all(),
            'marcas' => Marca::all(),
            'estados' => ['disponible', 'agotado', 'no disponible']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Articulos/Create', [
            'tipos' => TipoArticulo::all(),
            'marcas' => Marca::all(),
            'estados' => ['disponible', 'agotado', 'no disponible']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:40',
            'eslogan' => 'required|string|max:100',
            'descripcion' => 'required|string|max:300',
            'recomendacion' => 'required|string|max:300',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:disponible,agotado,no disponible',
            'tipo_articulo_id' => 'required|exists:tipo_articulos,id',
            'marca_id' => 'nullable|exists:marcas,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'eslogan.required' => 'El eslogan es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'recomendacion.required' => 'La recomendación es obligatoria.',
            'image_url.required' => 'La imagen es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'estado.required' => 'El estado es obligatorio.',
            'tipo_articulo_id.required' => 'El tipo de artículo es obligatorio.',
        ]);

        // Manejar la carga de imagen
        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('articulos', 'public');
            $validated['image_url'] = $path;
        }

        Articulo::create($validated);

        return redirect()->route('articulos.index')
            ->with('success', 'Artículo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Articulo $articulo)
    {
        $articulo->load(['tipoArticulo', 'marca']);

        return Inertia::render('Articulos/Show', [
            'articulo' => $articulo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articulo $articulo)
    {
        $articulo->load(['tipoArticulo', 'marca']);

        return Inertia::render('Articulos/Edit', [
            'articulo' => $articulo,
            'tipos' => TipoArticulo::all(),
            'marcas' => Marca::all(),
            'estados' => ['disponible', 'agotado', 'no disponible']
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:40',
            'eslogan' => 'required|string|max:100',
            'descripcion' => 'required|string|max:300',
            'recomendacion' => 'required|string|max:300',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:disponible,agotado,no disponible',
            'tipo_articulo_id' => 'required|exists:tipo_articulos,id',
            'marca_id' => 'nullable|exists:marcas,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'eslogan.required' => 'El eslogan es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'recomendacion.required' => 'La recomendación es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'estado.required' => 'El estado es obligatorio.',
            'tipo_articulo_id.required' => 'El tipo de artículo es obligatorio.',
        ]);

        // Manejar la carga de nueva imagen
        if ($request->hasFile('image_url')) {
            // Eliminar imagen anterior
            if ($articulo->image_url) {
                Storage::disk('public')->delete($articulo->image_url);
            }
            $path = $request->file('image_url')->store('articulos', 'public');
            $validated['image_url'] = $path;
        }

        $articulo->update($validated);

        return redirect()->route('articulos.index')
            ->with('success', 'Artículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articulo $articulo)
    {
        try {
            // Eliminar imagen del storage
            if ($articulo->image_url) {
                Storage::disk('public')->delete($articulo->image_url);
            }

            $articulo->delete();

            return redirect()->route('articulos.index')
                ->with('success', 'Artículo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')
                ->with('error', 'No se pudo eliminar el artículo. Puede estar asociado a otros registros.');
        }
    }
}
