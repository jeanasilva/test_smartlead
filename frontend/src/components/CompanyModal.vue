<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
      <div class="flex items-center justify-between pb-3">
        <h3 class="text-lg font-bold text-gray-900">
          {{ isEditMode ? 'Editar Empresa' : 'Nova Empresa' }}
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="form-label">
            Nome da Empresa *
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="form-input"
            :class="{ 'border-red-300': errors.name }"
            placeholder="Digite o nome da empresa"
          >
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">
            {{ errors.name }}
          </p>
        </div>

        <div>
          <label for="identifier" class="form-label">
            Identificador *
          </label>
          <input
            id="identifier"
            v-model="form.identifier"
            type="text"
            required
            class="form-input"
            :class="{ 'border-red-300': errors.identifier }"
            placeholder="Digite o identificador da empresa"
          >
          <p v-if="errors.identifier" class="mt-1 text-sm text-red-600">
            {{ errors.identifier }}
          </p>
        </div>

        <div>
          <label for="description" class="form-label">
            Descrição
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="form-textarea"
            :class="{ 'border-red-300': errors.description }"
            placeholder="Digite a descrição da empresa"
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">
            {{ errors.description }}
          </p>
        </div>

        <div class="flex items-center justify-end pt-6 space-x-3 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="btn btn-secondary"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="btn btn-primary"
          >
            <span v-if="loading" class="spinner mr-2"></span>
            {{ isEditMode ? 'Atualizar' : 'Criar' }} Empresa
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CompanyModal',
  props: {
    company: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: {
        name: '',
        identifier: '',
        description: ''
      },
      errors: {},
      loading: false
    }
  },
  computed: {
    isEditMode() {
      return !!this.company
    }
  },
  created() {
    if (this.company) {
      this.form = {
        name: this.company.name || '',
        identifier: this.company.identifier || '',
        description: this.company.description || ''
      }
    }
  },
  methods: {
    async handleSubmit() {
      this.errors = {}
      this.loading = true

      try {
        const url = this.isEditMode ? `/companies/${this.company.id}` : '/companies'
        const method = this.isEditMode ? 'put' : 'post'

        await this.$http[method](url, this.form)

        this.$swal.fire({
          title: `Empresa ${this.isEditMode ? 'atualizada' : 'criada'}!`,
          text: `A empresa foi ${this.isEditMode ? 'atualizada' : 'criada'} com sucesso.`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })

        this.$emit('saved')
      } catch (error) {
        console.error('Erro ao salvar empresa:', error)

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          this.$swal.fire({
            title: 'Erro',
            text: error.response?.data?.message || 'Não foi possível salvar a empresa.',
            icon: 'error'
          })
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
