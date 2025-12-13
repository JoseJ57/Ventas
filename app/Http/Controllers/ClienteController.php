<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Mostrar todos los clientes.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return response()->json($clientes, 200);
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'celular'   => 'required|string|max:20',
            'frecuente' => 'boolean'
        ]);

        $cliente = Cliente::create($validated);

        return response()->json([
            'message' => 'Cliente creado correctamente',
            'data'    => $cliente
        ], 201);
    }

    /**
     * Mostrar un cliente específico.
     */
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Actualizar un cliente.
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre'    => 'string|max:100',
            'apellido'  => 'string|max:100',
            'celular'   => 'string|max:20',
            'frecuente' => 'boolean'
        ]);

        $cliente->update($validated);

        return response()->json([
            'message' => 'Cliente actualizado correctamente',
            'data'    => $cliente
        ], 200);
    }

    /**
     * Eliminar un cliente.
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
    }
}
