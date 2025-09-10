<template>
  <AppLayout
    page-title="Gerenciamento de Tarefas"
    page-description="Organize, acompanhe e gerencie todas as suas tarefas em um só lugar"
    :breadcrumbs="breadcrumbs"
  >
    <template v-slot:pageActions>
      <div class="page-actions-container">
        <button @click="refreshData" class="action-btn refresh-btn" :disabled="loading">
          <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
          <span>Atualizar</span>
        </button>
        <button @click="openTaskModal()" class="new-task-btn">
          <i class="fas fa-plus"></i>
          <span>Nova Tarefa</span>
        </button>
      </div>
    </template>

    <!-- Stats Cards -->
    <div class="stats-section">
      <SkeletonLoader v-if="loading && !statsLoaded" type="stats" :items="4" />
      <div v-else class="stats-grid">
        <div v-for="stat in stats" :key="stat.id" class="stat-card" @click="filterByStatus(stat)">
          <div class="stat-card-content">
            <div class="stat-icon" :style="`background: ${stat.color}15; color: ${stat.color}`">
              <i :class="stat.icon"></i>
            </div>
            <div class="stat-info">
              <div class="stat-value">{{ stat.value }}</div>
              <div class="stat-label">{{ stat.label }}</div>
              <div class="stat-change" :class="stat.trend">
                <i :class="stat.trendIcon"></i>
                <span>{{ stat.change }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
      <!-- Sidebar with Filters -->
      <div class="sidebar">
        <div class="filters-card">
          <div class="card-header">
            <div class="header-content">
              <div class="header-title">
                <i class="fas fa-filter"></i>
                <h3>Filtros</h3>
              </div>
              <button v-if="hasFilters" @click="clearFilters" class="clear-btn">
                <i class="fas fa-times"></i>
                <span>Limpar</span>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="filter-group">
              <label class="filter-label">
                <i class="fas fa-search"></i>
                Pesquisar
              </label>
              <input
                v-model="filters.search"
                @input="handleSearch"
                type="text"
                placeholder="Digite para buscar..."
                class="filter-input"
                :disabled="filterLoading"
              />
            </div>

            <div class="filter-group">
              <label class="filter-label">
                <i class="fas fa-tasks"></i>
                Status
              </label>
              <select v-model="filters.status" class="filter-select" :disabled="filterLoading">
                <option value="">Todos os status</option>
                <option value="pendente">Pendente</option>
                <option value="em_andamento">Em Andamento</option>
                <option value="concluida">Concluída</option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label">
                <i class="fas fa-exclamation-triangle"></i>
                Prioridade
              </label>
              <select v-model="filters.priority" class="filter-select" :disabled="filterLoading">
                <option value="">Todas as prioridades</option>
                <option value="alta">Alta</option>
                <option value="media">Média</option>
                <option value="baixa">Baixa</option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label">
                <i class="fas fa-user"></i>
                Responsável
              </label>
              <select v-model="filters.user_id" class="filter-select" :disabled="filterLoading">
                <option value="">Todos os usuários</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                  {{ user.name }}
                </option>
              </select>
            </div>
            
            <!-- Filter Actions -->
            <div class="filter-actions">
              <button @click="applyFilters" class="filter-apply-btn" :disabled="filterLoading">
                <i v-if="filterLoading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-search"></i>
                {{ filterLoading ? 'Aplicando...' : 'Aplicar Filtros' }}
              </button>
              <button v-if="hasFilters" @click="clearFilters" class="filter-clear-btn" :disabled="filterLoading">
                <i class="fas fa-times"></i>
                Limpar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Tasks Area -->
      <div class="tasks-area">
        <div class="tasks-header">
          <div class="header-left">
            <h2 class="tasks-title">
              <i class="fas fa-clipboard-list"></i>
              Suas Tarefas
              <span class="tasks-count">({{ pagination.total }})</span>
            </h2>
            <div class="view-controls">
              <button
                @click="viewMode = 'grid'"
                :class="['view-btn', { active: viewMode === 'grid' }]"
                title="Visualização em Grade"
              >
                <i class="fas fa-th-large"></i>
              </button>
              <button
                @click="viewMode = 'list'"
                :class="['view-btn', { active: viewMode === 'list' }]"
                title="Visualização em Lista"
              >
                <i class="fas fa-list"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="(loading && !tasksLoaded) || filterLoading" class="loading-container">
          <div v-if="filterLoading" class="filter-loading-overlay">
            <div class="filter-loading-content">
              <i class="fas fa-spinner fa-spin"></i>
              <p>Aplicando filtros...</p>
            </div>
          </div>
          <SkeletonLoader type="card" :items="6" />
        </div>

        <!-- Tasks Content -->
        <div v-else-if="tasks.length > 0" class="tasks-container">
          <!-- Grid View -->
          <div v-if="viewMode === 'grid'" class="tasks-grid">
            <div v-for="task in tasks" :key="`task-${task.id}`" class="task-card" @click="openTaskModal(task)">
              <div class="task-card-header">
                <div class="task-priority" :class="`priority-${task.priority}`">
                  <i :class="getPriorityIcon(task.priority)"></i>
                  <span>{{ getPriorityLabel(task.priority) }}</span>
                </div>
                <div class="task-actions" @click.stop>
                  <button @click="openTaskModal(task)" class="action-btn edit" title="Editar">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteTask(task)" class="action-btn delete" title="Excluir">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </div>

              <div class="task-card-body">
                <h4 class="task-title">{{ task.title }}</h4>
                <p class="task-description">{{ task.description || 'Sem descrição disponível' }}</p>

                <div class="task-meta">
                  <div class="meta-row">
                    <div class="meta-item">
                      <i class="fas fa-building"></i>
                      <span>{{ task.company?.name || 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-user"></i>
                      <span>{{ task.user?.name || 'N/A' }}</span>
                    </div>
                  </div>
                  <div class="meta-row">
                    <div class="meta-item">
                      <i class="fas fa-calendar-plus"></i>
                      <span>{{ formatDate(task.created_at) }}</span>
                    </div>
                    <div v-if="task.due_date" class="meta-item due-date">
                      <i class="fas fa-calendar-times"></i>
                      <span>{{ formatDate(task.due_date) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="task-card-footer">
                <div class="task-status" :class="`status-${task.status}`">
                  <i :class="getStatusIcon(task.status)"></i>
                  <span>{{ getStatusLabel(task.status) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- List View -->
          <div v-else class="tasks-list">
            <div v-for="task in tasks" :key="`task-list-${task.id}`" class="task-list-item" @click="openTaskModal(task)">
              <div class="task-content">
                <div class="task-main-info">
                  <div class="task-header-row">
                    <h4 class="task-title">{{ task.title }}</h4>
                    <div class="task-badges">
                      <div class="task-priority" :class="`priority-${task.priority}`">
                        <i :class="getPriorityIcon(task.priority)"></i>
                        <span>{{ getPriorityLabel(task.priority) }}</span>
                      </div>
                      <div class="task-status" :class="`status-${task.status}`">
                        <i :class="getStatusIcon(task.status)"></i>
                        <span>{{ getStatusLabel(task.status) }}</span>
                      </div>
                    </div>
                  </div>
                  <p class="task-description">{{ task.description || 'Sem descrição disponível' }}</p>
                  <div class="task-meta-list">
                    <div class="meta-item">
                      <i class="fas fa-building"></i>
                      <span>{{ task.company?.name || 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-user"></i>
                      <span>{{ task.user?.name || 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-calendar-plus"></i>
                      <span>{{ formatDate(task.created_at) }}</span>
                    </div>
                    <div v-if="task.due_date" class="meta-item due-date">
                      <i class="fas fa-calendar-times"></i>
                      <span>{{ formatDate(task.due_date) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="task-actions" @click.stop>
                <button @click="openTaskModal(task)" class="action-btn edit" title="Editar">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteTask(task)" class="action-btn delete" title="Excluir">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="pagination">
            <div class="pagination-info">
              Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} tarefas
            </div>
            <div class="pagination-controls">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="pagination-btn"
              >
                <i class="fas fa-chevron-left"></i>
                <span>Anterior</span>
              </button>
              <span class="pagination-current">
                Página {{ pagination.current_page }} de {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="pagination-btn"
              >
                <span>Próximo</span>
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-illustration">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <h3 class="empty-title">
            {{ hasFilters ? 'Nenhuma tarefa encontrada' : 'Suas tarefas aparecerão aqui' }}
          </h3>
          <p class="empty-description">
            {{ hasFilters 
              ? 'Tente ajustar os filtros para encontrar o que procura ou crie uma nova tarefa.' 
              : 'Comece organizando seu trabalho criando sua primeira tarefa.' 
            }}
          </p>
          <button @click="hasFilters ? clearFilters() : openTaskModal()" class="create-task-btn">
            <i class="fas fa-plus"></i>
            <span>{{ hasFilters ? 'Ver Todas as Tarefas' : 'Criar Primeira Tarefa' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Task Modal -->
    <TaskModal
      v-if="showTaskModal"
      :task="selectedTask"
      :loading="modalLoading"
      :companies="[]"
      @close="closeTaskModal"
      @save="handleTaskSaved"
      @saved="handleModalSaved"
      @loading="handleModalLoading"
      @delete="handleTaskDeleted"
    />
  </AppLayout>
</template>

<script>
import AppLayout from '@/components/layout/AppLayout.vue'
import TaskModal from '@/components/TaskModal.vue'
import SkeletonLoader from '@/components/common/SkeletonLoader.vue'
import { taskService, userService } from '@/services/api'

export default {
  name: 'Tasks',
  components: {
    AppLayout,
    TaskModal,
    SkeletonLoader
  },
  data() {
    return {
      loading: false,
      filterLoading: false,
      viewMode: 'grid',
      showTaskModal: false,
      selectedTask: null,
      modalLoading: false,
      tasks: [],
      users: [],
      stats: [],
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
      tasksLoaded: false,
      statsLoaded: false,
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
    // Método para atualizar dados (garantindo que sempre atualize após ações)
    async refreshData() {
      this.loading = true
      this.tasksLoaded = false
      this.statsLoaded = false
      
      try {
        await Promise.all([
          this.loadTasks(),
          this.loadStats()
        ])
      } finally {
        this.loading = false
      }
    },

    // Método para aplicar filtros (com debounce para pesquisa)
    applyFilters() {
      this.pagination.current_page = 1
      this.loadTasks()
    },

    // Método para filtrar por status (clique nos cards de estatísticas)
    filterByStatus(stat) {
      if (stat.id === 2) { // Concluídas
        this.filters.status = 'concluida'
      } else if (stat.id === 3) { // Em Andamento
        this.filters.status = 'em_andamento'
      } else if (stat.id === 4) { // Pendentes
        this.filters.status = 'pendente'
      } else {
        this.filters.status = ''
      }
      this.applyFilters()
    },

    async loadTasks(fromFilter = false) {
      if (fromFilter) {
        this.filterLoading = true
      } else if (!this.loading) {
        this.loading = true
      }
      
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

        // Force reactive updates
        this.$forceUpdate()

      } catch (error) {
        console.error('Erro ao carregar tarefas:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Erro ao carregar as tarefas. Tente novamente.',
          icon: 'error',
          toast: true,
          position: 'top-end',
          timer: 3000,
          showConfirmButton: false
        })
      } finally {
        this.loading = false
        this.filterLoading = false
        this.tasksLoaded = true
      }
    },

    async loadStats() {
      try {
        const response = await taskService.list({ per_page: 1000 })
        const allTasks = response.data.data || []

        const total = allTasks.length
        const completed = allTasks.filter(task => task.status === 'concluida').length
        const pending = allTasks.filter(task => task.status === 'pendente').length
        const inProgress = allTasks.filter(task => task.status === 'em_andamento').length

        this.stats = [
          {
            id: 1,
            label: 'Total de Tarefas',
            value: total,
            icon: 'fas fa-tasks',
            color: '#3b82f6',
            trend: 'positive',
            trendIcon: 'fas fa-arrow-up',
            change: '+0%'
          },
          {
            id: 2,
            label: 'Concluídas',
            value: completed,
            icon: 'fas fa-check-circle',
            color: '#10b981',
            trend: 'positive',
            trendIcon: 'fas fa-arrow-up',
            change: total > 0 ? `${Math.round((completed / total) * 100)}%` : '0%'
          },
          {
            id: 3,
            label: 'Em Andamento',
            value: inProgress,
            icon: 'fas fa-clock',
            color: '#f59e0b',
            trend: 'neutral',
            trendIcon: 'fas fa-minus',
            change: total > 0 ? `${Math.round((inProgress / total) * 100)}%` : '0%'
          },
          {
            id: 4,
            label: 'Pendentes',
            value: pending,
            icon: 'fas fa-exclamation-triangle',
            color: '#ef4444',
            trend: 'negative',
            trendIcon: 'fas fa-arrow-down',
            change: total > 0 ? `${Math.round((pending / total) * 100)}%` : '0%'
          }
        ]

      } catch (error) {
        console.error('Erro ao carregar estatísticas:', error)
      } finally {
        this.statsLoaded = true
      }
    },

    calculateStats() {
      // Mantém compatibilidade com código antigo
      const completed = this.tasks.filter(task => task.status === 'concluida').length
      const pending = this.tasks.filter(task => task.status === 'pendente').length

      this.stats.completed = completed
      this.stats.pending = pending
      this.stats.total = this.pagination.total
    },
    
    async loadUsers() {
      try {
        const response = await userService.list({ per_page: 100 })
        this.users = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
      }
    },
    
    async openTaskModal(task = null) {
      console.log('openTaskModal chamado:', task)
      
      if (task && task.id) {
        // Se é edição, buscar dados atualizados da API
        try {
          this.modalLoading = true
          const response = await taskService.show(task.id)
          this.selectedTask = response.data.data
          console.log('Dados da task carregados:', this.selectedTask)
        } catch (error) {
          console.error('Erro ao carregar dados da task:', error)
          this.$swal.fire({
            title: 'Erro',
            text: 'Erro ao carregar dados da task. Tente novamente.',
            icon: 'error'
          })
          return
        } finally {
          this.modalLoading = false
        }
      } else {
        // Nova task
        this.selectedTask = null
      }
      
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
      // Atualizar dados após salvar
      await this.refreshData()
    },

    handleModalLoading(loading) {
      this.modalLoading = loading
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
          toast: true,
          position: 'top-end',
          timer: 3000,
          showConfirmButton: false
        })
        
        // Atualizar dados após salvar
        await this.refreshData()
        
        this.closeTaskModal()
        
        // Garantir que os dados sejam atualizados
        await this.refreshData()
        
      } catch (error) {
        console.error('Erro ao salvar tarefa:', error)
        this.$swal.fire({
          title: 'Erro',
          text: error.response?.data?.message || 'Erro ao salvar tarefa. Tente novamente.',
          icon: 'error'
        })
      } finally {
        this.modalLoading = false
      }
    },
    
    async handleTaskDeleted() {
      this.closeTaskModal()
      await this.refreshData()
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
        cancelButtonText: 'Cancelar',
        reverseButtons: true
      })
      
      if (result.isConfirmed) {
        try {
          await taskService.delete(task.id)
          
          this.$swal.fire({
            title: 'Sucesso!',
            text: 'Tarefa excluída com sucesso',
            icon: 'success',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
          })
          
          // Garantir que os dados sejam atualizados
          await this.refreshData()
          
        } catch (error) {
          console.error('Erro ao excluir tarefa:', error)
          this.$swal.fire({
            title: 'Erro',
            text: 'Erro ao excluir a tarefa. Tente novamente.',
            icon: 'error'
          })
        }
      }
    },
    
    handleSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1
        this.loadTasks(true)
      }, 300) // Diminui o tempo de debounce para melhor experiência
    },
    
    applyFilters() {
      this.pagination.current_page = 1
      this.loadTasks(true)
    },
    
    clearFilters() {
      this.filters = {
        status: '',
        priority: '',
        user_id: '',
        search: ''
      }
      this.pagination.current_page = 1
      this.loadTasks(true)
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page
        this.loadTasks()
        // Scroll to top when changing pages
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    },

    getViewBtnStyle(mode) {
      const isActive = this.viewMode === mode
      return {
        padding: '8px 10px',
        border: '1px solid #e5e7eb',
        borderRadius: '6px',
        background: isActive ? 'white' : '#f8fafc',
        cursor: 'pointer'
      }
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date)
    },
    
    getPriorityIcon(priority) {
      return {
        alta: 'fas fa-exclamation-triangle',
        media: 'fas fa-exclamation-circle',
        baixa: 'fas fa-info-circle'
      }[priority] || 'fas fa-info-circle'
    },

    getStatusIcon(status) {
      return {
        pendente: 'fas fa-clock',
        em_andamento: 'fas fa-play-circle',
        concluida: 'fas fa-check-circle'
      }[status] || 'fas fa-clock'
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
    }
  },

  async mounted() {
    await Promise.all([
      this.loadTasks(),
      this.loadUsers(),
      this.loadStats()
    ])
  }
}
</script>

<style scoped>
/* =============================================================================
   MODERN TASK MANAGEMENT PAGE STYLES
   ============================================================================= */

/* Page Actions */
.page-actions-container {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  font-size: 14px;
  font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  border: none;
  min-height: 44px;
}

.refresh-btn {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  color: #475569;
  border: 1px solid #e2e8f0;
}

.refresh-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.primary-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.primary-btn:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
}

/* Stats Section */
.stats-section {
  margin-bottom: 32px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.stat-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.stat-card:hover::before {
  transform: scaleX(1);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

.stat-card-content {
  display: flex;
  align-items: center;
  gap: 20px;
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  margin-bottom: 8px;
}

.stat-change {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
}

.stat-change.positive { color: #10b981; }
.stat-change.negative { color: #ef4444; }
.stat-change.neutral { color: #f59e0b; }

/* Main Content Layout */
.main-content {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 32px;
  align-items: start;
}

/* Sidebar Filters */
.sidebar {
  position: sticky;
  top: 24px;
}

.filters-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
}

.card-header {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e2e8f0;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-title i {
  color: #3b82f6;
  font-size: 18px;
}

.header-title h3 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.clear-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  color: #dc2626;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-btn:hover {
  background: #fecaca;
  transform: translateY(-1px);
}

.card-body {
  padding: 24px;
}

.filter-group {
  margin-bottom: 24px;
}

.filter-group:last-child {
  margin-bottom: 0;
}

.filter-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.filter-label i {
  color: #6b7280;
  width: 16px;
}

.filter-input,
.filter-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px;
  background: white;
  transition: all 0.3s ease;
}

.filter-input:focus,
.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-input::placeholder {
  color: #9ca3af;
}

/* Tasks Area */
.tasks-area {
  min-height: 600px;
}

.tasks-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.tasks-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.tasks-title i {
  color: #3b82f6;
}

.tasks-count {
  font-size: 18px;
  color: #6b7280;
  font-weight: 500;
}

.view-controls {
  display: flex;
  gap: 4px;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 10px;
}

.view-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 36px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #64748b;
  background: transparent;
}

.view-btn:hover {
  color: #3b82f6;
  background: rgba(59, 130, 246, 0.1);
}

.view-btn.active {
  background: white;
  color: #3b82f6;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Loading State */
.loading-container {
  min-height: 400px;
}

/* Tasks Grid */
.tasks-container {
  min-height: 400px;
}

.tasks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.task-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.task-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

.task-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 20px 20px 0 20px;
}

.task-priority {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.task-priority.priority-alta {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  color: #dc2626;
  border: 1px solid #fca5a5;
}

.task-priority.priority-media {
  background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
  color: #d97706;
  border: 1px solid #fbbf24;
}

.task-priority.priority-baixa {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  color: #2563eb;
  border: 1px solid #93c5fd;
}

.task-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 14px;
}

.action-btn.edit {
  background: #f0f9ff;
  color: #0369a1;
  border: 1px solid #bae6fd;
}

.action-btn.edit:hover {
  background: #e0f2fe;
  transform: translateY(-1px);
}

.action-btn.delete {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.action-btn.delete:hover {
  background: #fee2e2;
  transform: translateY(-1px);
}

.task-card-body {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.task-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 12px 0;
  line-height: 1.4;
}

.task-description {
  font-size: 14px;
  color: #64748b;
  line-height: 1.6;
  margin: 0 0 20px 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

.task-meta {
  margin-top: auto;
}

.meta-row {
  display: flex;
  gap: 16px;
  margin-bottom: 8px;
}

.meta-row:last-child {
  margin-bottom: 0;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #6b7280;
  flex: 1;
  min-width: 0;
}

.meta-item i {
  color: #9ca3af;
  flex-shrink: 0;
  width: 16px;
}

.meta-item span {
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}

.meta-item.due-date {
  color: #dc2626;
  font-weight: 600;
}

.task-card-footer {
  padding: 0 20px 20px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.task-status {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.task-status.status-pendente {
  background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
  color: #d97706;
  border: 1px solid #fbbf24;
}

.task-status.status-em_andamento {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  color: #2563eb;
  border: 1px solid #93c5fd;
}

.task-status.status-concluida {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  color: #16a34a;
  border: 1px solid #86efac;
}

/* Tasks List View */
.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
}

.task-list-item {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
}

.task-list-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
  border-color: #cbd5e1;
}

.task-content {
  flex: 1;
}

.task-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
  gap: 16px;
}

.task-badges {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.task-meta-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 16px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 40px;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border: 2px dashed #e2e8f0;
  border-radius: 20px;
  margin: 40px 0;
}

.empty-illustration {
  font-size: 72px;
  color: #cbd5e1;
  margin-bottom: 24px;
}

.empty-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 12px 0;
}

.empty-description {
  font-size: 16px;
  color: #64748b;
  line-height: 1.6;
  margin: 0 0 32px 0;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.create-task-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 32px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.create-task-btn:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 0;
  margin-top: 32px;
  border-top: 1px solid #e2e8f0;
}

.pagination-info {
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 16px;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  color: #374151;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-1px);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.pagination-current {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  padding: 0 8px;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .main-content {
    grid-template-columns: 280px 1fr;
    gap: 24px;
  }
  
  .tasks-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 1024px) {
  .main-content {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  
  .sidebar {
    position: static;
    order: -1;
  }
  
  .filters-card {
    margin-bottom: 0;
  }
  
  .card-body {
    padding: 20px;
  }
  
  .filter-group {
    margin-bottom: 20px;
  }
  
  .tasks-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }
}

@media (max-width: 768px) {
  .page-actions-container {
    flex-direction: column;
    gap: 8px;
    width: 100%;
  }
  
  .action-btn {
    width: 100%;
    justify-content: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .tasks-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  
  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .tasks-grid {
    grid-template-columns: 1fr;
  }
  
  .task-list-item {
    flex-direction: column;
    gap: 16px;
  }
  
  .task-header-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .task-badges {
    align-self: flex-start;
  }
  
  .task-actions {
    align-self: flex-end;
    margin-top: 16px;
  }
  
  .task-meta-list {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  
  .meta-row {
    flex-direction: column;
    gap: 12px;
  }
  
  .pagination {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
  
  .pagination-controls {
    justify-content: center;
  }
  
  .empty-state {
    padding: 60px 24px;
  }
  
  .empty-illustration {
    font-size: 56px;
  }
  
  .empty-title {
    font-size: 20px;
  }
  
  .empty-description {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .tasks-title {
    font-size: 20px;
  }
  
  .task-card-header,
  .task-card-body,
  .task-card-footer {
    padding: 16px;
  }
  
  .task-list-item {
    padding: 20px;
  }
  
  .card-body {
    padding: 16px;
  }
  
  .header-content {
    padding: 16px;
  }
  
  .stat-card {
    padding: 20px;
  }
  
  .stat-card-content {
    gap: 16px;
  }
  
  .stat-icon {
    width: 48px;
    height: 48px;
    font-size: 20px;
  }
  
  .stat-value {
    font-size: 28px;
  }
}

/* Focus States for Accessibility */
.action-btn:focus,
.filter-input:focus,
.filter-select:focus,
.pagination-btn:focus,
.create-task-btn:focus,
.clear-btn:focus,
.view-btn:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Loading animations */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.fa-spin {
  animation: fa-spin 1s infinite linear;
}

/* Custom scrollbar for filter areas */
.card-body {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}

.card-body::-webkit-scrollbar {
  width: 6px;
}

.card-body::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Filter Actions */
.filter-actions {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-apply-btn {
  width: 100%;
  padding: 12px 16px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.filter-apply-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1e40af);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.filter-apply-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.filter-clear-btn {
  width: 100%;
  padding: 10px 16px;
  background: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.filter-clear-btn:hover:not(:disabled) {
  background: #f1f5f9;
  color: #475569;
  border-color: #cbd5e1;
}

.filter-clear-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* New Task Button */
.new-task-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 24px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  min-width: 160px;
  justify-content: center;
}

.new-task-btn:hover {
  background: linear-gradient(135deg, #059669, #047857);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
}

.new-task-btn:active {
  transform: translateY(0);
}

.new-task-btn i {
  font-size: 14px;
}

.new-task-btn span {
  font-weight: 600;
}

/* Filter Loading Overlay */
.filter-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 12px;
}

.filter-loading-content {
  text-align: center;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.filter-loading-content i {
  font-size: 24px;
  color: #3b82f6;
  margin-bottom: 8px;
}

.filter-loading-content p {
  margin: 0;
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
}

.loading-container {
  position: relative;
}

/* Disabled state for filters */
.filter-input:disabled,
.filter-select:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: #f8fafc;
}
</style>

