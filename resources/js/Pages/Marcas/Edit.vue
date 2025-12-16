<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    marca: Object,
});

const form = useForm({
    nombre: props.marca.nombre,
    empresa: props.marca.empresa,
    pais: props.marca.pais || '',
});

const submit = () => {
    form.put(route('marcas.update', props.marca.id));
};
</script>

<template>
    <Head title="Editar Marca" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Marca</h2>
                <Link
                    :href="route('marcas.index')"
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
                            <!-- Nombre -->
                            <div class="mb-6">
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Marca <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    placeholder="Ej: Nike, Adidas, Puma"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.nombre }"
                                    maxlength="30"
                                />
                                <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.nombre }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ form.nombre.length }}/30 caracteres
                                </div>
                            </div>

                            <!-- Empresa -->
                            <div class="mb-6">
                                <label for="empresa" class="block text-sm font-medium text-gray-700 mb-2">
                                    Empresa <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="empresa"
                                    v-model="form.empresa"
                                    type="text"
                                    placeholder="Ej: Nike Inc., Adidas AG"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.empresa }"
                                    maxlength="30"
                                />
                                <div v-if="form.errors.empresa" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.empresa }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ form.empresa.length }}/30 caracteres
                                </div>
                            </div>

                            <!-- País -->
                            <div class="mb-6">
                                <label for="pais" class="block text-sm font-medium text-gray-700 mb-2">
                                    País <span class="text-gray-400">(Opcional)</span>
                                </label>
                                <input
                                    id="pais"
                                    v-model="form.pais"
                                    type="text"
                                    placeholder="Ej: Estados Unidos, Alemania, Italia"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.pais }"
                                    maxlength="50"
                                />
                                <div v-if="form.errors.pais" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.pais }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ form.pais.length }}/50 caracteres
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-600">
                                    <strong>ID:</strong> {{ marca.id }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Fecha de creación:</strong> {{ new Date(marca.created_at).toLocaleString() }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Última actualización:</strong> {{ new Date(marca.updated_at).toLocaleString() }}
                                </p>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3">
                                <Link
                                    :href="route('marcas.index')"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Actualizando...' : 'Actualizar Marca' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
