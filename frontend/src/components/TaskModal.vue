<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="flex items-center justify-between pb-3">
        <h3 class="text-lg font-bold text-gray-900">
          {{ isEditMode ? 'Editar Tarefa' : 'Nova Tarefa' }}
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label for="title" class="form-label">
              Título *
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="form-input"
              :class="{ 'border-red-300': errors.title }"
              placeholder="Digite o título da tarefa"
            >
            <p v-if="errors.title" class="mt-1 text-sm text-red-600">
              {{ errors.title }}
            </p>
          </div>

          <div class="md:col-span-2">
            <label for="description" class="form-label">
              Descrição
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="form-input"
              :class="{ 'border-red-300': errors.description }"
              placeholder="Descreva os detalhes da tarefa"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">
              {{ errors.description }}
            </p>
          </div>

          <div>
            <label for="priority" class="form-label">
              Prioridade *
            </label>
            <select
              id="priority"
              v-model="form.priority"
              required
              class="form-select"
              :class="{ 'border-red-300': errors.priority }"
            >
              <option value="">Selecione a prioridade</option>
              <option value="low">Baixa</option>
              <option value="medium">Média</option>
              <option value="high">Alta</option>
            </select>
            <p v-if="errors.priority" class="mt-1 text-sm text-red-600">
              {{ errors.priority }}
            </p>
          </div>

          <div>
            <label for="status" class="form-label">
              Status
            </label>
            <select
              id="status"
              v-model="form.status"
              class="form-select"
            >
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Progresso</option>
              <option value="completed">Concluída</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>

          <div>
            <label for="due_date" class="form-label">
              Data de Vencimento *
            </label>
            <input
              id="due_date"
              v-model="form.due_date"
              type="date"
              required
              class="form-input"
              :class="{ 'border-red-300': errors.due_date }"
              :min="today"
            >
            <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">
              {{ errors.due_date }}
            </p>
          </div>

          <div>
            <label for="assigned_to" class="form-label">
              Responsável
            </label>
            <select
              id="assigned_to"
              v-model="form.assigned_to_id"
              class="form-select"
            >
              <option value="">Selecione um usuário</option>
              <option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
              >
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>
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
            {{ isEditMode ? 'Atualizar' : 'Criar' }} Tarefa
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TaskModal',
  props: {
    task: {
      type: Object,
      default: null
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      form: {
        title: '',
        description: '',
        priority: '',
        status: 'pending',
        due_date: '',
        assigned_to_id: ''
      },
      errors: {},
      loading: false,
      users: []
    }
  },
  computed: {
    isEditMode() {
      return !!this.task
    },
    today() {
      return new Date().toISOString().split('T')[0]
    }
  },
  async created() {
    await this.loadUsers()
    
    if (this.task) {
      this.form = {
        title: this.task.title || '',
        description: this.task.description || '',
        priority: this.task.priority || '',
        status: this.task.status || 'pending',
        due_date: this.task.due_date ? this.task.due_date.split(' ')[0] : '',
        assigned_to_id: this.task.assigned_to_id || ''
      }
    }
  },
  methods: {
    async loadUsers() {
      try {
        const response = await this.$http.get('/users')
        this.users = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
      }
    },

    async handleSubmit() {
      this.errors = {}
      this.loading = true

      try {
        const url = this.isEditMode ? `/tasks/${this.task.id}` : '/tasks'
        const method = this.isEditMode ? 'put' : 'post'

        await this.$http[method](url, this.form)

        this.$swal.fire({
          title: `Tarefa ${this.isEditMode ? 'atualizada' : 'criada'}!`,
          text: `A tarefa foi ${this.isEditMode ? 'atualizada' : 'criada'} com sucesso.`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })

        this.$emit('saved')
        this.$emit('close')
      } catch (error) {
        console.error('Erro ao salvar tarefa:', error)

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          this.$swal.fire({
            title: 'Erro',
            text: error.response?.data?.message || 'Não foi possível salvar a tarefa.',
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
