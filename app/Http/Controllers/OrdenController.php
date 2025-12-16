<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\DetalleOrden;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Articulo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $ordenes = Orden::with(['cliente', 'empleado', 'detalles'])
            ->when($search, function ($query, $search) {
                $query->search($search);
            })
            ->when($estado, function ($query, $estado) {
                $query->byEstado($estado);
            })
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->byFecha($fechaInicio, $fechaFin);
            })
            ->orderBy('Fecha', 'desc')
            ->paginate(15)
            ->withQueryString();

        $estados = ['Pendiente', 'En Proceso', 'Completada', 'Cancelada', 'Entregada'];

        return Inertia::render('Ordenes/Index', [
            'ordenes' => $ordenes,
            'estados' => $estados,
            'filters' => [
                'search' => $search,
                'estado' => $estado,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::select('ID_Cliente_NUMBER', 'Nombre_Cliente', 'Apellido')
            ->orderBy('Nombre_Cliente')
            ->get()
            ->map(function($cliente) {
                return [
                    'value' => $cliente->ID_Cliente_NUMBER,
                    'label' => $cliente->Nombre_Cliente . ' ' . $cliente->Apellido,
                ];
            });

        $empleados = Empleado::select('ID_Empleado_NUMBER', 'Nombre_Empleado', 'Apellido')
            ->orderBy('Nombre_Empleado')
            ->get()
            ->map(function($empleado) {
                return [
                    'value' => $empleado->ID_Empleado_NUMBER,
                    'label' => $empleado->Nombre_Empleado . ' ' . $empleado->Apellido,
                ];
            });

        $articulos = Articulo::select('ID_Articulo_NUMBER', 'Nombre_Articulo', 'Precio', 'Descripcion')
            ->orderBy('Nombre_Articulo')
            ->get()
            ->map(function($articulo) {
                return [
                    'value' => $articulo->ID_Articulo_NUMBER,
                    'label' => $articulo->Nombre_Articulo,
                    'precio' => $articulo->Precio,
                    'descripcion' => $articulo->Descripcion,
                ];
            });

        $estados = ['Pendiente', 'En Proceso', 'Completada', 'Cancelada', 'Entregada'];
        $formasPago = ['Efectivo', 'Tarjeta', 'Transferencia', 'QR'];
        $tiposDetalle = ['Venta', 'Alquiler', 'Servicio'];
        $estadosDetalle = ['Pendiente', 'Procesado', 'Entregado', 'Devuelto'];

        return Inertia::render('Ordenes/Create', [
            'clientes' => $clientes,
            'empleados' => $empleados,
            'articulos' => $articulos,
            'estados' => $estados,
            'formasPago' => $formasPago,
            'tiposDetalle' => $tiposDetalle,
            'estadosDetalle' => $estadosDetalle,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Fecha' => 'required|date',
            'Forma_de_pago' => 'required|string|max:10',
            'Estado_Dio' => 'required|string|max:50',
            'Envio_Bool' => 'required|boolean',
            'ID_Cliente' => 'required|exists:cliente,ID_Cliente_NUMBER',
            'ID_Empleado' => 'nullable|exists:empleado,ID_Empleado_NUMBER',
            'detalles' => 'required|array|min:1',
            'detalles.*.ID_Articulo' => 'required|exists:articulo,ID_Articulo_NUMBER',
            'detalles.*.Cantidad' => 'required|integer|min:1',
            'detalles.*.Descuento' => 'nullable|numeric|min:0',
            'detalles.*.Observacion' => 'nullable|string',
            'detalles.*.Estado' => 'required|string|max:50',
            'detalles.*.Tipo' => 'required|string|max:50',
        ], [
            'Fecha.required' => 'La fecha es obligatoria.',
            'Forma_de_pago.required' => 'La forma de pago es obligatoria.',
            'Estado_Dio.required' => 'El estado es obligatorio.',
            'ID_Cliente.required' => 'Debe seleccionar un cliente.',
            'ID_Cliente.exists' => 'El cliente seleccionado no existe.',
            'detalles.required' => 'Debe agregar al menos un artículo.',
            'detalles.min' => 'Debe agregar al menos un artículo.',
        ]);

        try {
            DB::beginTransaction();

            // Calcular el total
            $total = 0;
            foreach ($validated['detalles'] as $detalle) {
                $articulo = Articulo::find($detalle['ID_Articulo']);
                $subtotal = ($articulo->Precio * $detalle['Cantidad']) - ($detalle['Descuento'] ?? 0);
                $total += $subtotal;
            }

            // Crear la orden
            $orden = Orden::create([
                'Fecha' => $validated['Fecha'],
                'Forma_de_pago' => $validated['Forma_de_pago'],
                'Total' => $total,
                'Estado_Dio' => $validated['Estado_Dio'],
                'Envio_Bool' => $validated['Envio_Bool'],
                'ID_Cliente' => $validated['ID_Cliente'],
                'ID_Empleado' => $validated['ID_Empleado'],
            ]);

            // Crear los detalles
            foreach ($validated['detalles'] as $detalle) {
                $articulo = Articulo::find($detalle['ID_Articulo']);
                $subtotal = ($articulo->Precio * $detalle['Cantidad']) - ($detalle['Descuento'] ?? 0);

                DetalleOrden::create([
                    'Cantidad' => $detalle['Cantidad'],
                    'Subtotal' => $subtotal,
                    'Observacion' => $detalle['Observacion'] ?? null,
                    'Estado' => $detalle['Estado'],
                    'Tipo' => $detalle['Tipo'],
                    'Descuento' => $detalle['Descuento'] ?? 0,
                    'ID_Articulo' => $detalle['ID_Articulo'],
                    'ID_Recibo_NUMBER' => $orden->ID_Recibo_NUMBER,
                ]);
            }

            DB::commit();

            return redirect()->route('ordenes.index')
                ->with('success', 'Orden creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la orden: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Orden $ordene)
    {
        $ordene->load(['cliente', 'empleado', 'detalles.articulo']);

        return Inertia::render('Ordenes/Show', [
            'orden' => $ordene
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orden $ordene)
    {
        $ordene->load(['cliente', 'empleado', 'detalles.articulo']);

        $clientes = Cliente::select('ID_Cliente_NUMBER', 'Nombre_Cliente', 'Apellido')
            ->orderBy('Nombre_Cliente')
            ->get()
            ->map(function($cliente) {
                return [
                    'value' => $cliente->ID_Cliente_NUMBER,
                    'label' => $cliente->Nombre_Cliente . ' ' . $cliente->Apellido,
                ];
            });

        $empleados = Empleado::select('ID_Empleado_NUMBER', 'Nombre_Empleado', 'Apellido')
            ->orderBy('Nombre_Empleado')
            ->get()
            ->map(function($empleado) {
                return [
                    'value' => $empleado->ID_Empleado_NUMBER,
                    'label' => $empleado->Nombre_Empleado . ' ' . $empleado->Apellido,
                ];
            });

        $articulos = Articulo::select('ID_Articulo_NUMBER', 'Nombre_Articulo', 'Precio', 'Descripcion')
            ->orderBy('Nombre_Articulo')
            ->get()
            ->map(function($articulo) {
                return [
                    'value' => $articulo->ID_Articulo_NUMBER,
                    'label' => $articulo->Nombre_Articulo,
                    'precio' => $articulo->Precio,
                    'descripcion' => $articulo->Descripcion,
                ];
            });

        $estados = ['Pendiente', 'En Proceso', 'Completada', 'Cancelada', 'Entregada'];
        $formasPago = ['Efectivo', 'Tarjeta', 'Transferencia', 'QR'];
        $tiposDetalle = ['Venta', 'Alquiler', 'Servicio'];
        $estadosDetalle = ['Pendiente', 'Procesado', 'Entregado', 'Devuelto'];

        return Inertia::render('Ordenes/Edit', [
            'orden' => $ordene,
            'clientes' => $clientes,
            'empleados' => $empleados,
            'articulos' => $articulos,
            'estados' => $estados,
            'formasPago' => $formasPago,
            'tiposDetalle' => $tiposDetalle,
            'estadosDetalle' => $estadosDetalle,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $ordene)
    {
        $validated = $request->validate([
            'Fecha' => 'required|date',
            'Forma_de_pago' => 'required|string|max:10',
            'Estado_Dio' => 'required|string|max:50',
            'Envio_Bool' => 'required|boolean',
            'ID_Cliente' => 'required|exists:cliente,ID_Cliente_NUMBER',
            'ID_Empleado' => 'nullable|exists:empleado,ID_Empleado_NUMBER',
            'detalles' => 'required|array|min:1',
            'detalles.*.id' => 'nullable|exists:detalle_orden,ID_Detalle_Recibo_NUMBER',
            'detalles.*.ID_Articulo' => 'required|exists:articulo,ID_Articulo_NUMBER',
            'detalles.*.Cantidad' => 'required|integer|min:1',
            'detalles.*.Descuento' => 'nullable|numeric|min:0',
            'detalles.*.Observacion' => 'nullable|string',
            'detalles.*.Estado' => 'required|string|max:50',
            'detalles.*.Tipo' => 'required|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Calcular el nuevo total
            $total = 0;
            foreach ($validated['detalles'] as $detalle) {
                $articulo = Articulo::find($detalle['ID_Articulo']);
                $subtotal = ($articulo->Precio * $detalle['Cantidad']) - ($detalle['Descuento'] ?? 0);
                $total += $subtotal;
            }

            // Actualizar la orden
            $ordene->update([
                'Fecha' => $validated['Fecha'],
                'Forma_de_pago' => $validated['Forma_de_pago'],
                'Total' => $total,
                'Estado_Dio' => $validated['Estado_Dio'],
                'Envio_Bool' => $validated['Envio_Bool'],
                'ID_Cliente' => $validated['ID_Cliente'],
                'ID_Empleado' => $validated['ID_Empleado'],
            ]);

            // Obtener IDs de detalles existentes
            $detalleIds = collect($validated['detalles'])
                ->pluck('id')
                ->filter()
                ->toArray();

            // Eliminar detalles que ya no están
            DetalleOrden::where('ID_Recibo_NUMBER', $ordene->ID_Recibo_NUMBER)
                ->whereNotIn('ID_Detalle_Recibo_NUMBER', $detalleIds)
                ->delete();

            // Actualizar o crear detalles
            foreach ($validated['detalles'] as $detalle) {
                $articulo = Articulo::find($detalle['ID_Articulo']);
                $subtotal = ($articulo->Precio * $detalle['Cantidad']) - ($detalle['Descuento'] ?? 0);

                if (isset($detalle['id'])) {
                    // Actualizar detalle existente
                    DetalleOrden::where('ID_Detalle_Recibo_NUMBER', $detalle['id'])
                        ->update([
                            'Cantidad' => $detalle['Cantidad'],
                            'Subtotal' => $subtotal,
                            'Observacion' => $detalle['Observacion'] ?? null,
                            'Estado' => $detalle['Estado'],
                            'Tipo' => $detalle['Tipo'],
                            'Descuento' => $detalle['Descuento'] ?? 0,
                            'ID_Articulo' => $detalle['ID_Articulo'],
                        ]);
                } else {
                    // Crear nuevo detalle
                    DetalleOrden::create([
                        'Cantidad' => $detalle['Cantidad'],
                        'Subtotal' => $subtotal,
                        'Observacion' => $detalle['Observacion'] ?? null,
                        'Estado' => $detalle['Estado'],
                        'Tipo' => $detalle['Tipo'],
                        'Descuento' => $detalle['Descuento'] ?? 0,
                        'ID_Articulo' => $detalle['ID_Articulo'],
                        'ID_Recibo_NUMBER' => $ordene->ID_Recibo_NUMBER,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('ordenes.index')
                ->with('success', 'Orden actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la orden: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orden $ordene)
    {
        try {
            $ordene->delete();

            return redirect()->route('ordenes.index')
                ->with('success', 'Orden eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('ordenes.index')
                ->with('error', 'No se pudo eliminar la orden: ' . $e->getMessage());
        }
    }
}
