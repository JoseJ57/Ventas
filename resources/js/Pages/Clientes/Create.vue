<script setup>
import { ref } from 'vue'
import axios from 'axios'

const form = ref({
    nombre: '',
    apellido: '',
    celular: '',
    frecuente: false
})

const enviar = async () => {
    await axios.post('/api/clientes', form.value)
    window.location.href = '/clientes'
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold">Crear Cliente</h1>

        <form @submit.prevent="enviar" class="mt-4">
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

            <button class="bg-blue-600 text-white p-2 rounded">Guardar</button>
        </form>
    </div>
</template>
