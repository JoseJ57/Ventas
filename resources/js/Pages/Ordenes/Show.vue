<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    orden: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'Pendiente': 'bg-yellow-100 text-yellow-800',
        'En Proceso': 'bg-blue-100 text-blue-800',
        'Completada': 'bg-green-100 text-green-800',
        'Cancelada': 'bg-red-100 text-red-800',
        'Entregada': 'bg-purple-100 text-purple-800',
        'Procesado': 'bg-blue-100 text-blue-800',
        'Entregado': 'bg-green-100 text-green-800',
        'Devuelto': 'bg-orange-100 text-orange-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`Orden #${orden.ID_Recibo_NUMBER}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Orden #{{ orden.ID_Recibo_NUMBER }}
                </h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('ordenes.edit', orden.ID_Recibo_NUMBER)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Editar
                    </Link>
                    <Link
                        :href="route('ordenes.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Información General -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Información General</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Fecha</p>
                                <p class="font-semibold">{{ new Date(orden.Fecha).toLocaleString() }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Cliente</p>
                                <p class="font-semibold">
                                    {{ orden.cliente?.Nombre_Cliente }} {{ orden.cliente?.Apellido }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Empleado</p>
                                <p class="font-semibold">
                                    {{ orden.empleado ? `${orden.empleado.Nombre_Empleado} ${orden.empleado.Apellido}` : 'No asignado' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Forma de Pago</p>
                                <p class="font-semibold">{{ orden.Forma_de_pago }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estado</p>
                                <span :class="['inline-block px-3 py-1 text-sm rounded-full', getEstadoBadgeClass(orden.Estado_Dio)]">
                                    {{ orden.Estado_Dio }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Envío</p>
                                <p class="font-semibold">{{ orden.Envio_Bool ? 'Sí' : 'No' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Detalles de la Orden</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artículo</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Descuento</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="detalle in orden.detalles" :key="detalle.ID_Detalle_Recibo_NUMBER" class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ detalle.articulo?.Nombre_Articulo }}</div>
                                            <div v-if="detalle.Observacion" class="text-xs text-gray-500 mt-1">
                                                {{ detalle.Observacion }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">{{ detalle.Cantidad }}</td>
                                        <td class="px-4 py-3 text-right">{{ formatCurrency(detalle.articulo?.Precio || 0) }}</td>
                                        <td class="px-4 py-3 text-right text-red-600">
                                            {{ detalle.Descuento > 0 ? formatCurrency(detalle.Descuento) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold">{{ formatCurrency(detalle.Subtotal) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                {{ detalle.Tipo }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span :class="['px-2 py-1 text-xs rounded', getEstadoBadgeClass(detalle.Estado)]">
                                                {{ detalle.Estado }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-900">Total:</td>
                                        <td class="px-4 py-3 text-right font-bold text-xl text-blue-600">
                                            {{ formatCurrency(orden.Total) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Información del Sistema</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Creado el</p>
                                <p class="font-semibold">{{ new Date(orden.created_at).toLocaleString() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Última actualización</p>
                                <p class="font-semibold">{{ new Date(orden.updated_at).toLocaleString() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Número de artículos</p>
                                <p class="font-semibold">{{ orden.detalles?.length || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
