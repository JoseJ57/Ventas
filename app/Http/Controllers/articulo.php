<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\TipoArticulo;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Articulo::with(['tipoArticulo', 'marca']);

        // Filtros opcionales
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tipo_articulo_id')) {
            $query->where('tipo_articulo_id', $request->tipo_articulo_id);
        }

        if ($request->has('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('eslogan', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $articulos = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($articulos);
    }

    /**
     * Get form data (tipos y marcas para el formulario)
     */
    public function formData()
    {
        return response()->json([
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
        ]);

        // Manejar la carga de imagen
        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('articulos', 'public');
            $validated['image_url'] = $path;
        }

        $articulo = Articulo::create($validated);
        $articulo->load(['tipoArticulo', 'marca']);

        return response()->json([
            'message' => 'Artículo creado exitosamente',
            'data' => $articulo
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Articulo $articulo)
    {
        $articulo->load(['tipoArticulo', 'marca']);
        return response()->json($articulo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:40',
            'eslogan' => 'sometimes|required|string|max:100',
            'descripcion' => 'sometimes|required|string|max:300',
            'recomendacion' => 'sometimes|required|string|max:300',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'precio' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|in:disponible,agotado,no disponible',
            'tipo_articulo_id' => 'sometimes|required|exists:tipo_articulos,id',
            'marca_id' => 'nullable|exists:marcas,id',
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
        $articulo->load(['tipoArticulo', 'marca']);

        return response()->json([
            'message' => 'Artículo actualizado exitosamente',
            'data' => $articulo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articulo $articulo)
    {
        // Eliminar imagen del storage
        if ($articulo->image_url) {
            Storage::disk('public')->delete($articulo->image_url);
        }

        $articulo->delete();

        return response()->json([
            'message' => 'Artículo eliminado exitosamente'
        ]);
    }
}
