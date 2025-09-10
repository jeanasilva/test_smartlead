<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
      <!-- Header -->
      <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center py-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Tarefas</h1>
              <p class="mt-2 text-sm text-gray-600">Gerencie suas tarefas e acompanhe o progresso</p>
            </div>
            <button 
              @click="openTaskModal()"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
              </svg>
              Nova Tarefa
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ pagination.total }}</h3>
                <p class="text-sm text-gray-600">Total de Tarefas</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ stats.completed }}</h3>
                <p class="text-sm text-gray-600">Concluídas</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ stats.pending }}</h3>
                <p class="text-sm text-gray-600">Pendentes</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg mb-6 border border-gray-100">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pesquisar</label>
                <input 
                  v-model="filters.search"
                  @input="handleSearch"
                  type="text" 
                  placeholder="Buscar tarefas..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select 
                  v-model="filters.status"
                  @change="loadTasks"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                  <option value="">Todos</option>
                  <option value="pendente">Pendente</option>
                  <option value="em_andamento">Em Andamento</option>
                  <option value="concluida">Concluída</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prioridade</label>
                <select 
                  v-model="filters.priority"
                  @change="loadTasks"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                  <option value="">Todas</option>
                  <option value="alta">Alta</option>
                  <option value="media">Média</option>
                  <option value="baixa">Baixa</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Usuário</label>
                <select 
                  v-model="filters.user_id"
                  @change="loadTasks"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                  <option value="">Todos</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
            </div>

            <div class="flex justify-end mt-4" v-if="hasFilters">
              <button 
                @click="clearFilters"
                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Limpar Filtros
              </button>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <p class="mt-2 text-gray-600">Carregando tarefas...</p>
        </div>

        <!-- Tasks Grid -->
        <div v-else-if="tasks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="task in tasks" 
            :key="task.id"
            class="bg-white rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-200"
          >
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ task.title }}</h3>
                  <p class="text-gray-600 text-sm">{{ task.description || 'Sem descrição' }}</p>
                </div>
                <div class="flex items-center space-x-2">
                  <span 
                    :class="getPriorityClass(task.priority)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ getPriorityLabel(task.priority) }}
                  </span>
                </div>
              </div>

              <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                  </svg>
                  {{ task.company?.name || 'N/A' }}
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                  </svg>
                  {{ task.user?.name || 'N/A' }}
                </div>
              </div>

              <div class="flex justify-between items-center">
                <span 
                  :class="getStatusClass(task.status)"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ getStatusLabel(task.status) }}
                </span>
                <div class="flex items-center space-x-2">
                  <button
                    @click.stop="openTaskModal(task)"
                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                    </svg>
                    Editar
                  </button>
                  <button
                    @click.stop="deleteTask(task)"
                    class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414L7.586 12l-1.293 1.293a1 1 0 101.414 1.414L9 13.414l1.293 1.293a1 1 0 001.414-1.414L10.414 12l1.293-1.293z" clip-rule="evenodd"></path>
                    </svg>
                    Excluir
                  </button>
                  <span class="text-xs text-gray-500">{{ formatDate(task.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma tarefa encontrada</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ hasFilters ? 'Tente ajustar os filtros para encontrar o que procura.' : 'Comece criando sua primeira tarefa.' }}
          </p>
          <div class="mt-6">
            <button 
              @click="openTaskModal()"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
              </svg>
              {{ hasFilters ? 'Criar Nova Tarefa' : 'Criar Primeira Tarefa' }}
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="tasks.length > 0 && pagination.last_page > 1" class="mt-8">
          <div class="bg-white px-4 py-3 border border-gray-200 rounded-lg sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              >
                Anterior
              </button>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              >
                Próxima
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando <span class="font-medium">{{ pagination.from }}</span> a 
                  <span class="font-medium">{{ pagination.to }}</span> de 
                  <span class="font-medium">{{ pagination.total }}</span> tarefas
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                  >
                    Anterior
                  </button>
                  <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    Página {{ pagination.current_page }} de {{ pagination.last_page }}
                  </span>
                  <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                  >
                    Próxima
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Modal -->
    <TaskModal
      :isVisible="showTaskModal"
      :task="selectedTask"
      :loading="modalLoading"
      :companies="[]"
      @close="closeTaskModal"
      @save="handleTaskSaved"
      @saved="handleModalSaved"
      @delete="handleTaskDeleted"
    />
  </AppLayout>
</template>

<script>
import AppLayout from '@/components/layout/AppLayout.vue'
import TaskModal from '@/components/TaskModal.vue'
import { taskService, userService } from '@/services/api'

export default {
  name: 'Tasks',
  components: {
    AppLayout,
    TaskModal
  },
  data() {
    return {
      loading: false,
      showTaskModal: false,
      selectedTask: null,
      modalLoading: false,
      tasks: [],
      users: [],
      stats: {
        completed: 0,
        pending: 0,
        total: 0
      },
      filters: {
        status: '',
        priority: '',
        user_id: '',
        search: ''
      },
      pagination: {
        current_page: 1,
        per_page: 12,
        total: 0,
        last_page: 1,
        from: 0,
        to: 0
      },
      searchTimeout: null,
      breadcrumbs: [
        { label: 'Dashboard', path: '/' },
        { label: 'Tarefas', path: '/tasks' }
      ]
    }
  },
  computed: {
    hasFilters() {
      return this.filters.search || this.filters.status || this.filters.priority || this.filters.user_id
    }
  },
  methods: {
    async loadTasks() {
      this.loading = true
      try {
        const params = {
          page: this.pagination.current_page,
          per_page: this.pagination.per_page,
          ...this.filters
        }
        
        // Remove empty filters
        Object.keys(params).forEach(key => {
          if (params[key] === '' || params[key] === null || params[key] === undefined) {
            delete params[key]
          }
        })
        
        const response = await taskService.list(params)
        
        this.tasks = response.data.data || []
        this.pagination = {
          current_page: response.data.current_page || 1,
          per_page: response.data.per_page || 12,
          total: response.data.total || 0,
          last_page: response.data.last_page || 1,
          from: response.data.from || 0,
          to: response.data.to || 0
        }
        
        // Calculate statistics
        this.calculateStats()
        
      } catch (error) {
        console.error('Erro ao carregar tarefas:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Erro ao carregar as tarefas',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },
    
    calculateStats() {
      const completed = this.tasks.filter(task => task.status === 'concluida').length
      const pending = this.tasks.filter(task => task.status === 'pendente').length
      
      this.stats = {
        completed,
        pending,
        total: this.pagination.total
      }
    },
    
    async loadUsers() {
      try {
        const response = await userService.list({ per_page: 100 })
        this.users = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
      }
    },
    
    openTaskModal(task = null) {
      console.log('openTaskModal chamado:', task)
      this.selectedTask = task
      this.showTaskModal = true
      console.log('showTaskModal:', this.showTaskModal)
    },
    
    closeTaskModal() {
      this.showTaskModal = false
      this.selectedTask = null
    },
    
    async handleModalSaved() {
      console.log('Modal saved event received')
      this.closeTaskModal()
      await this.loadTasks()
    },

    async handleTaskSaved(formData) {
      this.modalLoading = true
      console.log('handleTaskSaved chamado com:', formData)
      
      try {
        let response
        if (this.selectedTask?.id) {
          console.log('Atualizando tarefa:', this.selectedTask.id)
          response = await taskService.update(this.selectedTask.id, formData)
        } else {
          console.log('Criando nova tarefa')
          response = await taskService.create(formData)
        }
        
        console.log('Resposta da API:', response)
        
        this.$swal.fire({
          title: 'Sucesso!',
          text: this.selectedTask?.id ? 'Tarefa atualizada com sucesso!' : 'Tarefa criada com sucesso!',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
        
        this.closeTaskModal()
        await this.loadTasks()
        
      } catch (error) {
        console.error('Erro ao salvar tarefa:', error)
        this.$swal.fire({
          title: 'Erro',
          text: error.response?.data?.message || 'Erro ao salvar tarefa',
          icon: 'error'
        })
      } finally {
        this.modalLoading = false
      }
    },
    
    async handleTaskDeleted() {
      this.closeTaskModal()
      await this.loadTasks()
    },
    
    async deleteTask(task) {
      const result = await this.$swal.fire({
        title: 'Excluir Tarefa',
        text: `Tem certeza que deseja excluir a tarefa "${task.title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
      })
      
      if (result.isConfirmed) {
        try {
          await taskService.delete(task.id)
          this.$swal.fire({
            title: 'Sucesso!',
            text: 'Tarefa excluída com sucesso',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          })
          this.loadTasks()
        } catch (error) {
          console.error('Erro ao excluir tarefa:', error)
          this.$swal.fire({
            title: 'Erro',
            text: 'Erro ao excluir a tarefa',
            icon: 'error'
          })
        }
      }
    },
    
    handleSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1
        this.loadTasks()
      }, 500)
    },
    
    clearFilters() {
      this.filters = {
        status: '',
        priority: '',
        user_id: '',
        search: ''
      }
      this.pagination.current_page = 1
      this.loadTasks()
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page
        this.loadTasks()
      }
    },
    
    getPriorityClass(priority) {
      return {
        alta: 'bg-red-100 text-red-800',
        media: 'bg-yellow-100 text-yellow-800', 
        baixa: 'bg-green-100 text-green-800'
      }[priority] || 'bg-gray-100 text-gray-800'
    },
    
    getStatusClass(status) {
      return {
        pendente: 'bg-yellow-100 text-yellow-800',
        em_andamento: 'bg-blue-100 text-blue-800',
        concluida: 'bg-green-100 text-green-800'
      }[status] || 'bg-gray-100 text-gray-800'
    },
    
    getPriorityLabel(priority) {
      return {
        alta: 'Alta',
        media: 'Média',
        baixa: 'Baixa'
      }[priority] || 'Baixa'
    },
    
    getStatusLabel(status) {
      return {
        pendente: 'Pendente',
        em_andamento: 'Em Andamento',
        concluida: 'Concluída'
      }[status] || 'Pendente'
    },
    
    formatDate(date) {
      return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    }
  },
  
  async mounted() {
    await Promise.all([
      this.loadTasks(),
      this.loadUsers()
    ])
  }
}
</script>

