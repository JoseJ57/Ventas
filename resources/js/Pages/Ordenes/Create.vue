<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    clientes: Array,
    empleados: Array,
    articulos: Array,
    estados: Array,
    formasPago: Array,
    tiposDetalle: Array,
    estadosDetalle: Array,
});

const form = useForm({
    Fecha: new Date().toISOString().slice(0, 16),
    Forma_de_pago: 'Efectivo',
    Estado_Dio: 'Pendiente',
    Envio_Bool: false,
    ID_Cliente: '',
    ID_Empleado: '',
    detalles: [],
});

// Agregar detalle vacío
const agregarDetalle = () => {
    form.detalles.push({
        ID_Articulo: '',
        Cantidad: 1,
        Descuento: 0,
        Observacion: '',
        Estado: 'Pendiente',
        Tipo: 'Venta',
        precio_unitario: 0,
    });
};

// Eliminar detalle
const eliminarDetalle = (index) => {
    form.detalles.splice(index, 1);
};

// Cuando se selecciona un artículo
const onArticuloChange = (index) => {
    const articuloId = form.detalles[index].ID_Articulo;
    const articulo = props.articulos.find(a => a.value === articuloId);
    if (articulo) {
        form.detalles[index].precio_unitario = parseFloat(articulo.precio);
    }
};

// Calcular subtotal de un detalle
const calcularSubtotal = (detalle) => {
    const subtotal = (detalle.precio_unitario * detalle.Cantidad) - (detalle.Descuento || 0);
    return subtotal > 0 ? subtotal : 0;
};

// Calcular total general
const totalGeneral = computed(() => {
    return form.detalles.reduce((sum, detalle) => sum + calcularSubtotal(detalle), 0);
});

// Formatear moneda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};

// Enviar formulario
const submit = () => {
    if (form.detalles.length === 0) {
        alert('Debe agregar al menos un artículo');
        return;
    }
    form.post(route('ordenes.store'));
};

// Agregar un detalle inicial
if (form.detalles.length === 0) {
    agregarDetalle();
}
</script>

<template>
    <Head title="Nueva Orden" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Orden</h2>
                <Link
                    :href="route('ordenes.index')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <!-- Información General -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Información General</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Fecha -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.Fecha"
                                        type="datetime-local"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                        :class="{ 'border-red-500': form.errors.Fecha }"
                                    />
                                    <div v-if="form.errors.Fecha" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.Fecha }}
                                    </div>
                                </div>

                                <!-- Cliente -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Cliente <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.ID_Cliente"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                        :class="{ 'border-red-500': form.errors.ID_Cliente }"
                                    >
                                        <option value="">Seleccione un cliente</option>
                                        <option v-for="cliente in clientes" :key="cliente.value" :value="cliente.value">
                                            {{ cliente.label }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.ID_Cliente" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.ID_Cliente }}
                                    </div>
                                </div>

                                <!-- Empleado -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Empleado <span class="text-gray-400">(Opcional)</span>
                                    </label>
                                    <select
                                        v-model="form.ID_Empleado"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    >
                                        <option value="">Seleccione un empleado</option>
                                        <option v-for="empleado in empleados" :key="empleado.value" :value="empleado.value">
                                            {{ empleado.label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Forma de Pago -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Forma de Pago <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.Forma_de_pago"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    >
                                        <option v-for="forma in formasPago" :key="forma" :value="forma">
                                            {{ forma }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Estado <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.Estado_Dio"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    >
                                        <option v-for="estado in estados" :key="estado" :value="estado">
                                            {{ estado }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Envío -->
                                <div class="flex items-center pt-7">
                                    <input
                                        v-model="form.Envio_Bool"
                                        type="checkbox"
                                        id="envio"
                                        class="w-4 h-4 text-blue-600 rounded"
                                    />
                                    <label for="envio" class="ml-2 text-sm font-medium text-gray-700">
                                        ¿Requiere envío?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de la Orden -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Detalles de la Orden</h3>
                                <button
                                    type="button"
                                    @click="agregarDetalle"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    + Agregar Artículo
                                </button>
                            </div>

                            <div v-if="form.errors.detalles" class="mb-4 text-sm text-red-600">
                                {{ form.errors.detalles }}
                            </div>

                            <!-- Lista de detalles -->
                            <div class="space-y-4">
                                <div
                                    v-for="(detalle, index) in form.detalles"
                                    :key="index"
                                    class="border border-gray-200 rounded-lg p-4 relative"
                                >
                                    <button
                                        type="button"
                                        @click="eliminarDetalle(index)"
                                        class="absolute top-2 right-2 text-red-600 hover:text-red-900"
                                    >
                                        ✕
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Artículo -->
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Artículo <span class="text-red-500">*</span>
                                            </label>
                                            <select
                                                v-model="detalle.ID_Articulo"
                                                @change="onArticuloChange(index)"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                                :class="{ 'border-red-500': form.errors[`detalles.${index}.ID_Articulo`] }"
                                            >
                                                <option value="">Seleccione un artículo</option>
                                                <option v-for="articulo in articulos" :key="articulo.value" :value="articulo.value">
                                                    {{ articulo.label }} - {{ formatCurrency(articulo.precio) }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Cantidad -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Cantidad <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model.number="detalle.Cantidad"
                                                type="number"
                                                min="1"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                            />
                                        </div>

                                        <!-- Precio Unitario -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Precio Unitario
                                            </label>
                                            <input
                                                :value="formatCurrency(detalle.precio_unitario)"
                                                type="text"
                                                readonly
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                                            />
                                        </div>

                                        <!-- Tipo -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tipo
                                            </label>
                                            <select
                                                v-model="detalle.Tipo"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                            >
                                                <option v-for="tipo in tiposDetalle" :key="tipo" :value="tipo">
                                                    {{ tipo }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Estado -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Estado
                                            </label>
                                            <select
                                                v-model="detalle.Estado"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                            >
                                                <option v-for="estado in estadosDetalle" :key="estado" :value="estado">
                                                    {{ estado }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Descuento -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Descuento
                                            </label>
                                            <input
                                                v-model.number="detalle.Descuento"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                            />
                                        </div>

                                        <!-- Subtotal -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Subtotal
                                            </label>
                                            <input
                                                :value="formatCurrency(calcularSubtotal(detalle))"
                                                type="text"
                                                readonly
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 font-semibold"
                                            />
                                        </div>

                                        <!-- Observación -->
                                        <div class="lg:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Observación
                                            </label>
                                            <textarea
                                                v-model="detalle.Observacion"
                                                rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                                placeholder="Observaciones adicionales..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="form.detalles.length === 0" class="text-center py-8 text-gray-500">
                                    No hay artículos. Haga clic en "Agregar Artículo" para comenzar.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold">Total de la Orden</h3>
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ formatCurrency(totalGeneral) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <Link
                            :href="route('ordenes.index')"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        >
                            {{ form.processing ? 'Guardando...' : 'Guardar Orden' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
