<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    id: Number
})

const form = ref({
    nombre: '',
    apellido: '',
    celular: '',
    frecuente: false
})

const cargar = async () => {
    const res = await axios.get(`/api/clientes/${props.id}`)
    form.value = res.data
}

const actualizar = async () => {
    await axios.put(`/api/clientes/${props.id}`, form.value)
    window.location.href = '/clientes'
}

onMounted(() => cargar())
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold">Editar Cliente</h1>

        <form @submit.prevent="actualizar" class="mt-4">

            <div class="mb-2">
                <label>Nombre</label>
                <input v-model="form.nombre" class="border p-2 w-full" />
            </div>

            <div class="mb-2">
                <label>Apellido</label>
                <input v-model="form.apellido" class="border p-2 w-full" />
            </div>

            <div class="mb-2">
                <label>Celular</label>
                <input v-model="form.celular" class="border p-2 w-full" />
            </div>

            <div class="mb-2">
                <label>Frecuente</label>
                <input type="checkbox" v-model="form.frecuente" />
            </div>

            <button class="bg-green-600 text-white p-2 rounded">Actualizar</button>
        </form>
    </div>
</template>
