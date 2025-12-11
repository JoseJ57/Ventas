<template>
  <div class="articulos-container">
    <div class="header">
      <h1>Gestión de Artículos</h1>
      <button @click="openModal()" class="btn btn-primary">
        <span class="icon">+</span> Nuevo Artículo
      </button>
    </div>

    <!-- Filtros -->
    <div class="filters">
      <input
        v-model="filters.search"
        @input="fetchArticulos"
        type="text"
        placeholder="Buscar por nombre, eslogan o descripción..."
        class="search-input"
      />
      <select v-model="filters.estado" @change="fetchArticulos" class="filter-select">
        <option value="">Todos los estados</option>
        <option value="disponible">Disponible</option>
        <option value="agotado">Agotado</option>
        <option value="no disponible">No disponible</option>
      </select>
      <select v-model="filters.tipo_articulo_id" @change="fetchArticulos" class="filter-select">
        <option value="">Todos los tipos</option>
        <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">
          {{ tipo.nombre }}
        </option>
      </select>
      <select v-model="filters.marca_id" @change="fetchArticulos" class="filter-select">
        <option value="">Todas las marcas</option>
        <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
          {{ marca.nombre }}
        </option>
      </select>
    </div>

    <!-- Tabla de artículos -->
    <div class="table-container">
      <table v-if="articulos.length > 0">
        <thead>
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Eslogan</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Tipo</th>
            <th>Marca</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="articulo in articulos" :key="articulo.id">
            <td>
              <img :src="getImageUrl(articulo.image_url)" :alt="articulo.nombre" class="thumbnail" />
            </td>
            <td>{{ articulo.nombre }}</td>
            <td>{{ articulo.eslogan }}</td>
            <td class="precio">Bs. {{ articulo.precio }}</td>
            <td>
              <span class="badge" :class="`badge-${articulo.estado}`">
                {{ articulo.estado }}
              </span>
            </td>
            <td>{{ articulo.tipo_articulo?.nombre || '-' }}</td>
            <td>{{ articulo.marca?.nombre || '-' }}</td>
            <td class="actions">
              <button @click="viewArticulo(articulo)" class="btn-icon btn-view" title="Ver">
                👁️
              </button>
              <button @click="openModal(articulo)" class="btn-icon btn-edit" title="Editar">
                ✏️
              </button>
              <button @click="deleteArticulo(articulo.id)" class="btn-icon btn-delete" title="Eliminar">
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="empty-state">
        <p>No se encontraron artículos</p>
      </div>
    </div>

    <!-- Paginación -->
    <div v-if="pagination.last_page > 1" class="pagination">
      <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1">
        Anterior
      </button>
      <span>Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
      <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">
        Siguiente
      </button>
    </div>

    <!-- Modal de formulario -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Editar' : 'Nuevo' }} Artículo</h2>
          <button @click="closeModal" class="btn-close">×</button>
        </div>
        <form @submit.prevent="submitForm" class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label>Nombre *</label>
              <input v-model="form.nombre" type="text" required maxlength="40" />
            </div>

            <div class="form-group">
              <label>Eslogan *</label>
              <input v-model="form.eslogan" type="text" required maxlength="100" />
            </div>

            <div class="form-group full-width">
              <label>Descripción *</label>
              <textarea v-model="form.descripcion" required maxlength="300" rows="3"></textarea>
            </div>

            <div class="form-group full-width">
              <label>Recomendación *</label>
              <textarea v-model="form.recomendacion" required maxlength="300" rows="3"></textarea>
            </div>

            <div class="form-group">
              <label>Precio (Bs.) *</label>
              <input v-model="form.precio" type="number" step="0.01" min="0" required />
            </div>

            <div class="form-group">
              <label>Estado *</label>
              <select v-model="form.estado" required>
                <option value="disponible">Disponible</option>
                <option value="agotado">Agotado</option>
                <option value="no disponible">No disponible</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tipo de Artículo *</label>
              <select v-model="form.tipo_articulo_id" required>
                <option value="">Seleccione...</option>
                <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">
                  {{ tipo.nombre }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label>Marca</label>
              <select v-model="form.marca_id">
                <option value="">Seleccione...</option>
                <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                  {{ marca.nombre }}
                </option>
              </select>
            </div>

            <div class="form-group full-width">
              <label>Imagen *</label>
              <input type="file" @change="handleFileUpload" accept="image/*" :required="!isEditing" />
              <img v-if="imagePreview" :src="imagePreview" class="image-preview" />
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Cancelar
            </button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de vista detallada -->
    <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
      <div class="modal modal-view">
        <div class="modal-header">
          <h2>{{ selectedArticulo?.nombre }}</h2>
          <button @click="showViewModal = false" class="btn-close">×</button>
        </div>
        <div class="modal-body" v-if="selectedArticulo">
          <img :src="getImageUrl(selectedArticulo.image_url)" :alt="selectedArticulo.nombre" class="view-image" />
          <div class="detail-grid">
            <div class="detail-item">
              <strong>Eslogan:</strong>
              <p>{{ selectedArticulo.eslogan }}</p>
            </div>
            <div class="detail-item">
              <strong>Descripción:</strong>
              <p>{{ selectedArticulo.descripcion }}</p>
            </div>
            <div class="detail-item">
              <strong>Recomendación:</strong>
              <p>{{ selectedArticulo.recomendacion }}</p>
            </div>
            <div class="detail-item">
              <strong>Precio:</strong>
              <p class="precio-large">Bs. {{ selectedArticulo.precio }}</p>
            </div>
            <div class="detail-item">
              <strong>Estado:</strong>
              <p><span class="badge" :class="`badge-${selectedArticulo.estado}`">{{ selectedArticulo.estado }}</span></p>
            </div>
            <div class="detail-item">
              <strong>Tipo:</strong>
              <p>{{ selectedArticulo.tipo_articulo?.nombre || '-' }}</p>
            </div>
            <div class="detail-item">
              <strong>Marca:</strong>
              <p>{{ selectedArticulo.marca?.nombre || '-' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ArticulosView',
  data() {
    return {
      articulos: [],
      tipos: [],
      marcas: [],
      pagination: {},
      filters: {
        search: '',
        estado: '',
        tipo_articulo_id: '',
        marca_id: ''
      },
      showModal: false,
      showViewModal: false,
      isEditing: false,
      loading: false,
      selectedArticulo: null,
      form: {
        nombre: '',
        eslogan: '',
        descripcion: '',
        recomendacion: '',
        precio: '',
        estado: 'disponible',
        tipo_articulo_id: '',
        marca_id: '',
        image_url: null
      },
      imagePreview: null
    };
  },
  mounted() {
    this.fetchFormData();
    this.fetchArticulos();
  },
  methods: {
    async fetchFormData() {
      try {
        const response = await axios.get('/api/articulos/form-data');
        this.tipos = response.data.tipos;
        this.marcas = response.data.marcas;
      } catch (error) {
        console.error('Error al cargar datos del formulario:', error);
      }
    },
    async fetchArticulos(page = 1) {
      try {
        const params = { ...this.filters, page };
        const response = await axios.get('/api/articulos', { params });
        this.articulos = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total
        };
      } catch (error) {
        console.error('Error al cargar artículos:', error);
      }
    },
    openModal(articulo = null) {
      this.isEditing = !!articulo;
      if (articulo) {
        this.form = { ...articulo, image_url: null };
        this.imagePreview = this.getImageUrl(articulo.image_url);
      } else {
        this.resetForm();
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },
    resetForm() {
      this.form = {
        nombre: '',
        eslogan: '',
        descripcion: '',
        recomendacion: '',
        precio: '',
        estado: 'disponible',
        tipo_articulo_id: '',
        marca_id: '',
        image_url: null
      };
      this.imagePreview = null;
      this.isEditing = false;
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.form.image_url = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    async submitForm() {
      this.loading = true;
      try {
        const formData = new FormData();
        Object.keys(this.form).forEach(key => {
          if (this.form[key] !== null && this.form[key] !== '') {
            formData.append(key, this.form[key]);
          }
        });

        if (this.isEditing) {
          formData.append('_method', 'PUT');
          await axios.post(`/api/articulos/${this.form.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
          alert('Artículo actualizado exitosamente');
        } else {
          await axios.post('/api/articulos', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
          alert('Artículo creado exitosamente');
        }
        this.closeModal();
        this.fetchArticulos();
      } catch (error) {
        console.error('Error al guardar artículo:', error);
        alert('Error al guardar el artículo');
      } finally {
        this.loading = false;
      }
    },
    async deleteArticulo(id) {
      if (!confirm('¿Está seguro de eliminar este artículo?')) return;
      try {
        await axios.delete(`/api/articulos/${id}`);
        alert('Artículo eliminado exitosamente');
        this.fetchArticulos();
      } catch (error) {
        console.error('Error al eliminar artículo:', error);
        alert('Error al eliminar el artículo');
      }
    },
    viewArticulo(articulo) {
      this.selectedArticulo = articulo;
      this.showViewModal = true;
    },
    changePage(page) {
      this.fetchArticulos(page);
    },
    getImageUrl(path) {
      return path ? `/storage/${path}` : '/placeholder.jpg';
    }
  }
};
</script>

<style scoped>
.articulos-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

h1 {
  font-size: 28px;
  color: #333;
}

.filters {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 15px;
  margin-bottom: 25px;
}

.search-input, .filter-select {
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
}

.table-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f8f9fa;
}

th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: #555;
  border-bottom: 2px solid #dee2e6;
}

td {
  padding: 15px;
  border-bottom: 1px solid #f0f0f0;
}

.thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
}

.precio {
  font-weight: 600;
  color: #28a745;
}

.badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.badge-disponible {
  background: #d4edda;
  color: #155724;
}

.badge-agotado {
  background: #fff3cd;
  color: #856404;
}

.badge-no.disponible {
  background: #f8d7da;
  color: #721c24;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn, .btn-icon {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-icon {
  padding: 6px 10px;
  font-size: 16px;
}

.btn-view:hover {
  background: #e7f3ff;
}

.btn-edit:hover {
  background: #fff3cd;
}

.btn-delete:hover {
  background: #f8d7da;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 25px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #dee2e6;
}

.btn-close {
  background: none;
  border: none;
  font-size: 32px;
  cursor: pointer;
  color: #999;
}

.modal-body {
  padding: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
}

.image-preview {
  margin-top: 10px;
  max-width: 200px;
  border-radius: 8px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid #dee2e6;
}

.view-image {
  width: 100%;
  max-height: 400px;
  object-fit: contain;
  border-radius: 8px;
  margin-bottom: 20px;
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.detail-item {
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.detail-item strong {
  display: block;
  margin-bottom: 8px;
  color: #555;
}

.detail-item p {
  margin: 0;
  color: #333;
}

.precio-large {
  font-size: 24px;
  font-weight: 700;
  color: #28a745;
}

.empty-state {
  padding: 60px;
  text-align: center;
  color: #999;
}

.icon {
  font-size: 18px;
}
</style>
