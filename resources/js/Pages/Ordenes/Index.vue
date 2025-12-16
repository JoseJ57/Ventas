<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    ordenes: Object,
    estados: Array,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters.search || '',
    estado: props.filters.estado || '',
    fecha_inicio: props.filters.fecha_inicio || '',
    fecha_fin: props.filters.fecha_fin || '',
});

// Búsqueda con debounce
let searchTimeout = null;
watch(() => [filterForm.search, filterForm.estado, filterForm.fecha_inicio, filterForm.fecha_fin], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('ordenes.index'), {
            search: filterForm.search,
            estado: filterForm.estado,
            fecha_inicio: filterForm.fecha_inicio,
            fecha_fin: filterForm.fecha_fin,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

const deleteOrden = (id) => {
    if (confirm('¿Estás seguro de eliminar esta orden? Se eliminarán también todos sus detalles.')) {
        router.delete(route('ordenes.destroy', id), {
            preserveScroll: true,
        });
    }
};

const clearFilters = () => {
    filterForm.search = '';
    filterForm.estado = '';
    filterForm.fecha_inicio = '';
    filterForm.fecha_fin = '';
};

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
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Órdenes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Órdenes</h2>
                <Link
                    :href="route('ordenes.create')"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Nueva Orden
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Mensajes -->
                <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <input
                                    v-model="filterForm.search"
                                    type="text"
                                    placeholder="Buscar por ID, cliente o estado..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                />
                            </div>
                            <div>
                                <select
                                    v-model="filterForm.estado"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                >
                                    <option value="">Todos los estados</option>
                                    <option v-for="estado in estados" :key="estado" :value="estado">
                                        {{ estado }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <input
                                    v-model="filterForm.fecha_inicio"
                                    type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                    placeholder="Fecha inicio"
                                />
                            </div>
                            <div class="flex gap-2">
                                <input
                                    v-model="filterForm.fecha_fin"
                                    type="date"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md"
                                    placeholder="Fecha fin"
                                />
                                <button
                                    v-if="filterForm.search || filterForm.estado || filterForm.fecha_inicio || filterForm.fecha_fin"
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md whitespace-nowrap"
                                >
                                    Limpiar
                                </button>
                            </div>
                        </div>

                        <!-- Tabla -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forma Pago</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Envío</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="orden in ordenes.data" :key="orden.ID_Recibo_NUMBER" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ orden.ID_Recibo_NUMBER }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(orden.Fecha).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ orden.cliente?.Nombre_Cliente }} {{ orden.cliente?.Apellido }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ formatCurrency(orden.Total) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="['px-2 py-1 text-xs rounded-full', getEstadoBadgeClass(orden.Estado_Dio)]">
                                                {{ orden.Estado_Dio }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ orden.Forma_de_pago }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span v-if="orden.Envio_Bool" class="text-green-600">✓</span>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('ordenes.show', orden.ID_Recibo_NUMBER)"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Ver
                                            </Link>
                                            <Link
                                                :href="route('ordenes.edit', orden.ID_Recibo_NUMBER)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                @click="deleteOrden(orden.ID_Recibo_NUMBER)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="ordenes.data.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No se encontraron órdenes
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="ordenes.links.length > 3" class="mt-4 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Mostrando {{ ordenes.from }} a {{ ordenes.to }} de {{ ordenes.total }} resultados
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="(link, index) in ordenes.links"
                                    :key="index"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'px-3 py-2 rounded-md text-sm font-medium',
                                        link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
