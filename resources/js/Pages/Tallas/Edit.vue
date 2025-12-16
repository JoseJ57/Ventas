<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    talla: Object,
});

const form = useForm({
    nombre: props.talla.nombre,
});

const submit = () => {
    form.put(route('tallas.update', props.talla.id));
};
</script>

<template>
    <Head title="Editar Talla" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Talla</h2>
                <Link
                    :href="route('tallas.index')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Talla <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    placeholder="Ej: S, M, L, XL"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.nombre }"
                                />
                                <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <div class="mb-6 p-4 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-600">
                                    <strong>ID:</strong> {{ talla.id }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Fecha de creación:</strong> {{ new Date(talla.created_at).toLocaleString() }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Última actualización:</strong> {{ new Date(talla.updated_at).toLocaleString() }}
                                </p>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <Link
                                    :href="route('tallas.index')"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Actualizando...' : 'Actualizar Talla' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
