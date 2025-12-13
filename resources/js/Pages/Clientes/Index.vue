<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const clientes = ref([])

const cargarClientes = async () => {
    const res = await axios.get('/api/clientes')
    clientes.value = res.data
}

const eliminar = async (id) => {
    if (!confirm('¿Eliminar cliente?')) return
    await axios.delete(`/api/clientes/${id}`)
    cargarClientes()
}

onMounted(() => {
    cargarClientes()
})
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Clientes</h1>

        <a href="/clientes/create" class="text-blue-600">Crear cliente</a>

        <table class="mt-4 w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">ID</th>
                    <th class="p-2">Nombre</th>
                    <th class="p-2">Apellido</th>
                    <th class="p-2">Celular</th>
                    <th class="p-2">Frecuente</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="c in clientes" :key="c.id">
                    <td class="p-2">{{ c.id }}</td>
                    <td class="p-2">{{ c.nombre }}</td>
                    <td class="p-2">{{ c.apellido }}</td>
                    <td class="p-2">{{ c.celular }}</td>
                    <td class="p-2">{{ c.frecuente ? 'Sí' : 'No' }}</td>
                    <td class="p-2">
                        <a :href="`/clientes/${c.id}/edit`" class="text-green-600">Editar</a>
                        &nbsp;|&nbsp;
                        <button @click="eliminar(c.id)" class="text-red-600">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
