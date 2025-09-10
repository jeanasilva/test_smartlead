<template>
  <AppLayout
    page-title="Tarefas"
    page-description="Gerencie suas tarefas e acompanhe o progresso"
    :breadcrumbs="breadcrumbs"
  >
    <template v-slot:pageActions>
      <button @click="openTaskModal()" class="create-btn" :style="createBtnStyle">
        <i class="fas fa-plus"></i>
        Nova Tarefa
      </button>
    </template>

    <!-- Filters Card -->
    <div class="filters-card" :style="cardStyle">
      <div class="card-header" :style="cardHeaderStyle">
        <h3 class="card-title">
          <i class="fas fa-filter"></i>
          Filtros
        </h3>
        <button @click="clearFilters" class="clear-filters-btn" :style="clearFiltersBtnStyle">
          <i class="fas fa-times"></i>
          Limpar
        </button>
      </div>
      <div class="filters-grid" :style="filtersGridStyle">
        <div class="filter-group">
          <label class="filter-label">Status</label>
          <select v-model="filters.status" @change="loadTasks" class="filter-select" :style="filterSelectStyle">
            <option value="">Todos os Status</option>
            <option value="pendente">Pendente</option>
            <option value="em_andamento">Em Andamento</option>
            <option value="concluida">Concluída</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label class="filter-label">Prioridade</label>
          <select v-model="filters.priority" @change="loadTasks" class="filter-select" :style="filterSelectStyle">
            <option value="">Todas as Prioridades</option>
            <option value="baixa">Baixa</option>
            <option value="media">Média</option>
            <option value="alta">Alta</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label class="filter-label">Responsável</label>
          <select v-model="filters.user_id" @change="loadTasks" class="filter-select" :style="filterSelectStyle">
            <option value="">Todos os Usuários</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        
        <div class="filter-group">
          <label class="filter-label">Buscar</label>
          <div class="search-input-container" :style="searchInputContainerStyle">
            <i class="fas fa-search search-icon"></i>
            <input
              v-model="filters.search"
              @input="debounceSearch"
              type="text"
              placeholder="Buscar por título ou descrição..."
              class="search-input"
              :style="searchInputStyle"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Tasks Grid/List -->
    <div class="tasks-container" :style="cardStyle">
      <div class="tasks-header" :style="cardHeaderStyle">
        <div class="tasks-count">
          <h3 class="card-title">
            <i class="fas fa-tasks"></i>
            Tarefas ({{ pagination.total }})
          </h3>
        </div>
        <div class="view-controls" :style="viewControlsStyle">
          <button 
            @click="viewMode = 'grid'"
            :class="{ 'active': viewMode === 'grid' }"
            class="view-btn"
            :style="getViewBtnStyle('grid')"
          >
            <i class="fas fa-th"></i>
          </button>
          <button 
            @click="viewMode = 'list'"
            :class="{ 'active': viewMode === 'list' }"
            class="view-btn"
            :style="getViewBtnStyle('list')"
          >
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-container" :style="loadingContainerStyle">
        <SkeletonLoader 
          :type="viewMode === 'grid' ? 'card' : 'list'" 
          :items="6"
        />
      </div>

      <!-- Tasks Content -->
      <div v-else-if="tasks.length > 0" class="tasks-content">
        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="tasks-grid" :style="tasksGridStyle">
          <div 
            v-for="task in tasks" 
            :key="task.id"
            class="task-card"
            :style="getTaskCardStyle(task.priority)"
            @click="openTaskModal(task)"
          >
            <div class="task-card-header">
              <div class="task-priority" :style="getTaskPriorityStyle(task.priority)">
                <i :class="getPriorityIcon(task.priority)"></i>
                {{ getPriorityLabel(task.priority) }}
              </div>
              <div class="task-actions">
                <button @click.stop="openTaskModal(task)" class="task-action-btn" :style="taskActionBtnStyle">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click.stop="deleteTask(task)" class="task-action-btn delete" :style="taskActionBtnStyle">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
            
            <div class="task-card-body">
              <h4 class="task-title">{{ task.title }}</h4>
              <p class="task-description">{{ task.description || 'Sem descrição' }}</p>
              
              <div class="task-meta" :style="taskMetaStyle">
                <div class="task-company">
                  <i class="fas fa-building"></i>
                  {{ task.company?.name || 'N/A' }}
                </div>
                <div class="task-user">
                  <i class="fas fa-user"></i>
                  {{ task.user?.name || 'N/A' }}
                </div>
                <div class="task-date">
                  <i class="fas fa-calendar"></i>
                  {{ formatDate(task.created_at) }}
                </div>
              </div>
            </div>
            
            <div class="task-card-footer">
              <div class="task-status" :style="getTaskStatusStyle(task.status)">
                {{ getStatusLabel(task.status) }}
              </div>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="tasks-list" :style="tasksListStyle">
          <div 
            v-for="task in tasks" 
            :key="task.id"
            class="task-list-item"
            :style="taskListItemStyle"
            @click="openTaskModal(task)"
          >
            <div class="task-list-content">
              <div class="task-list-main">
                <h4 class="task-title">{{ task.title }}</h4>
                <p class="task-description">{{ task.description || 'Sem descrição' }}</p>
              </div>
              
              <div class="task-list-meta" :style="taskListMetaStyle">
                <div class="task-company">
                  <i class="fas fa-building"></i>
                  {{ task.company?.name || 'N/A' }}
                </div>
                <div class="task-user">
                  <i class="fas fa-user"></i>
                  {{ task.user?.name || 'N/A' }}
                </div>
                <div class="task-priority" :style="getTaskPriorityStyle(task.priority)">
                  <i :class="getPriorityIcon(task.priority)"></i>
                  {{ getPriorityLabel(task.priority) }}
                </div>
                <div class="task-status" :style="getTaskStatusStyle(task.status)">
                  {{ getStatusLabel(task.status) }}
                </div>
                <div class="task-date">
                  {{ formatDate(task.created_at) }}
                </div>
              </div>
            </div>
            
            <div class="task-list-actions">
              <button @click.stop="openTaskModal(task)" class="task-action-btn" :style="taskActionBtnStyle">
                <i class="fas fa-edit"></i>
              </button>
              <button @click.stop="deleteTask(task)" class="task-action-btn delete" :style="taskActionBtnStyle">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="empty-state" :style="emptyStateStyle">
        <div class="empty-icon">
          <i class="fas fa-tasks"></i>
        </div>
        <h3>Nenhuma tarefa encontrada</h3>
        <p>{{ hasFilters ? 'Nenhuma tarefa corresponde aos filtros aplicados.' : 'Você ainda não criou nenhuma tarefa.' }}</p>
        <button @click="openTaskModal()" class="empty-action-btn" :style="emptyActionBtnStyle">
          <i class="fas fa-plus"></i>
          Criar primeira tarefa
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="tasks.length > 0 && pagination.last_page > 1" class="pagination-container" :style="paginationContainerStyle">
        <div class="pagination-info">
          Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} tarefas
        </div>
        <div class="pagination-controls" :style="paginationControlsStyle">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="pagination-btn"
            :style="paginationBtnStyle"
          >
            Anterior
          </button>
          <span class="pagination-current">
            Página {{ pagination.current_page }} de {{ pagination.last_page }}
          </span>
          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="pagination-btn"
            :style="paginationBtnStyle"
          >
            Próxima
          </button>
        </div>
      </div>
    </div>

    <!-- Task Modal -->
    <TaskModal
      :is-visible="showTaskModal"
      :task="selectedTask"
      :loading="modalLoading"
      @close="closeTaskModal"
      @submit="handleTaskSaved"
    />
  </AppLayout>
</template>

<script>
import AppLayout from '@/components/layout/AppLayout.vue'
import SkeletonLoader from '@/components/common/SkeletonLoader.vue'
import TaskModal from '@/components/modals/TaskModal.vue'
import { taskService, userService } from '@/services/api'

export default {
  name: 'Tasks',
  components: {
    AppLayout,
    SkeletonLoader,
    TaskModal
  },
  data() {
    return {
      loading: false,
      viewMode: 'grid', // 'grid' or 'list'
      showTaskModal: false,
      selectedTask: null,
      modalLoading: false,
      tasks: [],
      users: [],
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
    },
    cardStyle() {
      return {
        background: 'white',
        borderRadius: '12px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
        border: '1px solid #e5e7eb',
        marginBottom: '24px'
      }
    },
    cardHeaderStyle() {
      return {
        padding: '20px 24px',
        borderBottom: '1px solid #f3f4f6',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between'
      }
    },
    filtersGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
        gap: '20px',
        padding: '24px'
      }
    },
    filterSelectStyle() {
      return {
        width: '100%',
        padding: '8px 12px',
        border: '1px solid #d1d5db',
        borderRadius: '6px',
        fontSize: '14px'
      }
    },
    searchInputContainerStyle() {
      return {
        position: 'relative'
      }
    },
    searchInputStyle() {
      return {
        width: '100%',
        padding: '8px 12px 8px 40px',
        border: '1px solid #d1d5db',
        borderRadius: '6px',
        fontSize: '14px'
      }
    },
    viewControlsStyle() {
      return {
        display: 'flex',
        gap: '4px',
        background: '#f8fafc',
        borderRadius: '6px',
        padding: '4px'
      }
    },
    tasksGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))',
        gap: '24px',
        padding: '24px'
      }
    },
    tasksListStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        gap: '16px',
        padding: '24px'
      }
    },
    taskListItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        padding: '20px',
        background: '#f9fafb',
        border: '1px solid #e5e7eb',
        borderRadius: '8px',
        cursor: 'pointer',
        transition: 'all 0.3s ease'
      }
    },
    taskListMetaStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(120px, 1fr))',
        gap: '16px',
        fontSize: '13px',
        color: '#6b7280',
        marginTop: '8px'
      }
    },
    taskMetaStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        gap: '8px',
        fontSize: '13px',
        color: '#6b7280',
        margin: '12px 0'
      }
    },
    taskActionBtnStyle() {
      return {
        width: '32px',
        height: '32px',
        border: 'none',
        borderRadius: '6px',
        cursor: 'pointer',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '14px',
        transition: 'all 0.3s ease',
        backgroundColor: '#f3f4f6',
        color: '#6b7280'
      }
    },
    emptyStateStyle() {
      return {
        textAlign: 'center',
        padding: '60px 20px',
        color: '#6b7280'
      }
    },
    loadingContainerStyle() {
      return {
        padding: '24px'
      }
    },
    paginationContainerStyle() {
      return {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: '24px',
        borderTop: '1px solid #f3f4f6',
        fontSize: '14px',
        color: '#6b7280'
      }
    },
    paginationControlsStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '16px'
      }
    },
    paginationBtnStyle() {
      return {
        padding: '8px 12px',
        border: '1px solid #d1d5db',
        borderRadius: '6px',
        background: 'white',
        cursor: 'pointer',
        transition: 'all 0.3s ease'
      }
    },
    createBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 20px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        border: 'none',
        borderRadius: '8px',
        cursor: 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        transition: 'all 0.3s ease',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)'
      }
    },
    clearFiltersBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '6px',
        padding: '8px 12px',
        background: '#f3f4f6',
        color: '#6b7280',
        border: 'none',
        borderRadius: '6px',
        cursor: 'pointer',
        fontSize: '13px',
        transition: 'all 0.3s ease'
      }
    },
    emptyActionBtnStyle() {
      return {
        display: 'inline-flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 24px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        border: 'none',
        borderRadius: '8px',
        cursor: 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        marginTop: '20px',
        transition: 'all 0.3s ease'
      }
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
    
    async loadUsers() {
      try {
        const response = await userService.list({ per_page: 100 })
        this.users = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
      }
    },
    
    openTaskModal(task = null) {
      this.selectedTask = task
      this.showTaskModal = true
    },
    
    closeTaskModal() {
      this.showTaskModal = false
      this.selectedTask = null
    },
    
    async handleTaskSaved(formData) {
      this.modalLoading = true
      
      try {
        if (this.selectedTask?.id) {
          await taskService.update(this.selectedTask.id, formData)
        } else {
          await taskService.create(formData)
        }
        
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
          text: 'Erro ao salvar tarefa',
          icon: 'error'
        })
      } finally {
        this.modalLoading = false
      }
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
    
    debounceSearch() {
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
    
    getViewBtnStyle(mode) {
      const isActive = this.viewMode === mode
      return {
        padding: '8px 12px',
        border: 'none',
        borderRadius: '4px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        fontSize: '14px',
        backgroundColor: isActive ? 'white' : 'transparent',
        color: isActive ? '#374151' : '#6b7280',
        boxShadow: isActive ? '0 1px 2px rgba(0, 0, 0, 0.1)' : 'none'
      }
    },
    
    getTaskCardStyle(priority) {
      const priorityColors = {
        alta: '#fef2f2',
        media: '#fefce8',
        baixa: '#f0f9ff'
      }
      
      return {
        background: priorityColors[priority] || '#ffffff',
        border: '1px solid #e5e7eb',
        borderRadius: '12px',
        padding: '20px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        height: '100%',
        display: 'flex',
        flexDirection: 'column'
      }
    },
    
    getTaskPriorityStyle(priority) {
      const styles = {
        alta: { background: '#fee2e2', color: '#991b1b', border: '1px solid #fca5a5' },
        media: { background: '#fef3c7', color: '#92400e', border: '1px solid #fcd34d' },
        baixa: { background: '#dbeafe', color: '#1e40af', border: '1px solid #93c5fd' }
      }
      
      return {
        ...styles[priority] || styles.baixa,
        padding: '4px 8px',
        borderRadius: '12px',
        fontSize: '11px',
        fontWeight: '600',
        display: 'flex',
        alignItems: 'center',
        gap: '4px',
        width: 'fit-content'
      }
    },
    
    getTaskStatusStyle(status) {
      const styles = {
        pendente: { background: '#fef3c7', color: '#92400e' },
        em_andamento: { background: '#dbeafe', color: '#1e40af' },
        concluida: { background: '#dcfce7', color: '#166534' }
      }
      
      return {
        ...styles[status] || styles.pendente,
        padding: '4px 12px',
        borderRadius: '20px',
        fontSize: '12px',
        fontWeight: '600'
      }
    },
    
    getPriorityIcon(priority) {
      return {
        alta: 'fas fa-exclamation-triangle',
        media: 'fas fa-exclamation-circle',
        baixa: 'fas fa-minus-circle'
      }[priority] || 'fas fa-minus-circle'
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

<style scoped>
.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.filter-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  z-index: 1;
}

.filter-select:focus,
.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.task-title {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.task-description {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.task-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.task-actions {
  display: flex;
  gap: 6px;
}

.task-card-body {
  flex: 1;
}

.task-card-footer {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}

.task-company,
.task-user,
.task-date {
  display: flex;
  align-items: center;
  gap: 6px;
}

.task-list-content {
  flex: 1;
}

.task-list-actions {
  display: flex;
  gap: 8px;
}

.task-list-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.task-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

.task-action-btn:hover {
  background-color: #e5e7eb !important;
  color: #374151 !important;
}

.task-action-btn.delete:hover {
  background-color: #fee2e2 !important;
  color: #dc2626 !important;
}

.empty-icon i {
  font-size: 64px;
  color: #d1d5db;
  margin-bottom: 24px;
}

.empty-state h3 {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
  margin: 0 0 8px 0;
}

.empty-state p {
  margin: 0 0 24px 0;
  color: #6b7280;
}

.create-btn:hover,
.pagination-btn:hover,
.clear-filters-btn:hover,
.empty-action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .filters-grid {
    grid-template-columns: 1fr !important;
  }
  
  .tasks-grid {
    grid-template-columns: 1fr !important;
  }
  
  .task-list-item {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }
  
  .task-list-actions {
    justify-content: center;
  }
  
  .pagination-container {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
}
</style>
