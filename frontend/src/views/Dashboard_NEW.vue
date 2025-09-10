<template>
  <AppLayout
    page-title="Dashboard"
    page-description="Visão geral das suas atividades e estatísticas"
    :breadcrumbs="breadcrumbs"
  >
    <!-- Stats Cards -->
    <div class="stats-grid" :style="statsGridStyle">
      <SkeletonLoader 
        v-if="loading && !statsLoaded" 
        type="card" 
        :items="4" 
      />
      <div 
        v-else 
        v-for="stat in stats" 
        :key="stat.id"
        class="stat-card" 
        :style="getStatCardStyle(stat.color)"
      >
        <div class="stat-icon" :style="getStatIconStyle(stat.color)">
          <i :class="stat.icon"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ stat.value }}</div>
          <div class="stat-label">{{ stat.label }}</div>
          <div class="stat-change" :style="getStatChangeStyle(stat.trend)">
            <i :class="stat.trendIcon"></i>
            {{ stat.change }}
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid" :style="dashboardGridStyle">
      <!-- Recent Tasks -->
      <div class="dashboard-card" :style="cardStyle">
        <div class="card-header" :style="cardHeaderStyle">
          <h3 class="card-title">
            <i class="fas fa-tasks"></i>
            Tarefas Recentes
          </h3>
          <router-link to="/tasks" class="card-link">Ver todas</router-link>
        </div>
        <div class="card-body">
          <SkeletonLoader 
            v-if="loading && !tasksLoaded" 
            type="list" 
            :items="5" 
          />
          <div v-else-if="recentTasks.length > 0" class="tasks-list">
            <div 
              v-for="task in recentTasks" 
              :key="task.id"
              class="task-item" 
              :style="taskItemStyle"
            >
              <div class="task-status" :style="getTaskStatusStyle(task.status)"></div>
              <div class="task-info">
                <div class="task-title">{{ task.title }}</div>
                <div class="task-meta">
                  <span class="task-priority" :style="getTaskPriorityStyle(task.priority)">
                    {{ getPriorityLabel(task.priority) }}
                  </span>
                  <span class="task-company">{{ task.company?.name || 'N/A' }}</span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="empty-state" :style="emptyStateStyle">
            <i class="fas fa-tasks"></i>
            <p>Nenhuma tarefa encontrada</p>
            <router-link to="/tasks" class="create-btn">Criar tarefa</router-link>
          </div>
        </div>
      </div>

      <!-- Recent Companies -->
      <div class="dashboard-card" :style="cardStyle">
        <div class="card-header" :style="cardHeaderStyle">
          <h3 class="card-title">
            <i class="fas fa-building"></i>
            Empresas Recentes
          </h3>
          <router-link to="/companies" class="card-link">Ver todas</router-link>
        </div>
        <div class="card-body">
          <SkeletonLoader 
            v-if="loading && !companiesLoaded" 
            type="list" 
            :items="3" 
          />
          <div v-else-if="recentCompanies.length > 0" class="companies-list">
            <div 
              v-for="company in recentCompanies" 
              :key="company.id"
              class="company-item" 
              :style="companyItemStyle"
            >
              <div class="company-avatar" :style="companyAvatarStyle">
                {{ getCompanyInitials(company.name) }}
              </div>
              <div class="company-info">
                <div class="company-name">{{ company.name }}</div>
                <div class="company-meta">
                  {{ company.tasks_count || 0 }} tarefas
                </div>
              </div>
            </div>
          </div>
          <div v-else class="empty-state" :style="emptyStateStyle">
            <i class="fas fa-building"></i>
            <p>Nenhuma empresa cadastrada</p>
            <router-link to="/companies" class="create-btn">Cadastrar empresa</router-link>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="dashboard-card" :style="cardStyle">
        <div class="card-header" :style="cardHeaderStyle">
          <h3 class="card-title">
            <i class="fas fa-bolt"></i>
            Ações Rápidas
          </h3>
        </div>
        <div class="card-body">
          <div class="quick-actions" :style="quickActionsStyle">
            <button 
              @click="$router.push('/tasks')"
              class="quick-action-btn"
              :style="quickActionBtnStyle"
            >
              <i class="fas fa-plus"></i>
              <span>Nova Tarefa</span>
            </button>
            
            <button 
              @click="$router.push('/companies')"
              class="quick-action-btn"
              :style="quickActionBtnStyle"
            >
              <i class="fas fa-building"></i>
              <span>Nova Empresa</span>
            </button>
            
            <button 
              @click="$router.push('/profile')"
              class="quick-action-btn"
              :style="quickActionBtnStyle"
            >
              <i class="fas fa-user"></i>
              <span>Meu Perfil</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/components/layout/AppLayout.vue'
import SkeletonLoader from '@/components/common/SkeletonLoader.vue'
import { taskService, companyService, dashboardService } from '@/services/api'

export default {
  name: 'Dashboard',
  components: {
    AppLayout,
    SkeletonLoader
  },
  data() {
    return {
      loading: true,
      statsLoaded: false,
      tasksLoaded: false,
      companiesLoaded: false,
      chartLoaded: false,
      stats: [],
      recentTasks: [],
      recentCompanies: [],
      chartData: [],
      breadcrumbs: [
        { label: 'Dashboard', path: '/' }
      ]
    }
  },
  computed: {
    statsGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
        gap: '24px',
        marginBottom: '32px'
      }
    },
    dashboardGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(400px, 1fr))',
        gap: '24px'
      }
    },
    cardStyle() {
      return {
        background: 'white',
        borderRadius: '16px',
        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        border: '1px solid #e5e7eb',
        overflow: 'hidden'
      }
    },
    cardHeaderStyle() {
      return {
        padding: '24px 24px 16px',
        borderBottom: '1px solid #f3f4f6',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center'
      }
    },
    taskItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        padding: '16px 24px',
        borderBottom: '1px solid #f3f4f6',
        transition: 'all 0.3s ease',
        cursor: 'pointer'
      }
    },
    companyItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        padding: '16px 24px',
        borderBottom: '1px solid #f3f4f6',
        transition: 'all 0.3s ease',
        cursor: 'pointer'
      }
    },
    companyAvatarStyle() {
      return {
        width: '48px',
        height: '48px',
        borderRadius: '50%',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '16px',
        fontWeight: '600',
        marginRight: '16px'
      }
    },
    emptyStateStyle() {
      return {
        textAlign: 'center',
        padding: '40px 24px',
        color: '#9ca3af'
      }
    },
    quickActionsStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        gap: '12px',
        padding: '24px'
      }
    },
    quickActionBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
        padding: '16px 20px',
        background: 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)',
        border: '2px solid transparent',
        borderRadius: '12px',
        color: '#475569',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        fontSize: '14px',
        fontWeight: '600'
      }
    }
  },
  methods: {
    async fetchStats() {
      try {
        const response = await dashboardService.stats()
        const data = response.data
        
        this.stats = [
          {
            id: 1,
            label: 'Total de Tarefas',
            value: data.total_tasks || '0',
            change: '+12%',
            trend: 'up',
            trendIcon: 'fas fa-arrow-up',
            icon: 'fas fa-tasks',
            color: 'blue'
          },
          {
            id: 2,
            label: 'Concluídas',
            value: data.completed_tasks || '0',
            change: '+8%',
            trend: 'up',
            trendIcon: 'fas fa-arrow-up',
            icon: 'fas fa-check-circle',
            color: 'green'
          },
          {
            id: 3,
            label: 'Pendentes',
            value: data.pending_tasks || '0',
            change: '-2%',
            trend: 'down',
            trendIcon: 'fas fa-arrow-down',
            icon: 'fas fa-clock',
            color: 'yellow'
          },
          {
            id: 4,
            label: 'Em Andamento',
            value: data.in_progress_tasks || '0',
            change: '+5%',
            trend: 'up',
            trendIcon: 'fas fa-arrow-up',
            icon: 'fas fa-play-circle',
            color: 'purple'
          }
        ]
        
        this.statsLoaded = true
      } catch (error) {
        console.error('Erro ao buscar estatísticas:', error)
        this.stats = [
          {
            id: 1,
            label: 'Total de Tarefas',
            value: '0',
            change: '0%',
            trend: 'neutral',
            trendIcon: 'fas fa-minus',
            icon: 'fas fa-tasks',
            color: 'blue'
          }
        ]
        this.statsLoaded = true
      }
    },
    
    async fetchRecentTasks() {
      try {
        const response = await dashboardService.recentTasks()
        this.recentTasks = response.data.data || []
        this.tasksLoaded = true
      } catch (error) {
        console.error('Erro ao buscar tarefas recentes:', error)
        this.recentTasks = []
        this.tasksLoaded = true
      }
    },
    
    async fetchRecentCompanies() {
      try {
        const response = await companyService.list({ per_page: 5 })
        this.recentCompanies = response.data.data || []
        this.companiesLoaded = true
      } catch (error) {
        console.error('Erro ao buscar empresas recentes:', error)
        this.recentCompanies = []
        this.companiesLoaded = true
      }
    },
    
    getStatCardStyle(color) {
      const colorMap = {
        blue: '#dbeafe',
        green: '#dcfce7',
        yellow: '#fef3c7',
        purple: '#e9d5ff'
      }
      
      return {
        background: colorMap[color] || '#f9fafb',
        padding: '24px',
        borderRadius: '16px',
        display: 'flex',
        alignItems: 'center',
        gap: '16px',
        border: '1px solid #e5e7eb'
      }
    },
    
    getStatIconStyle(color) {
      const colorMap = {
        blue: '#1e40af',
        green: '#166534',
        yellow: '#92400e',
        purple: '#7c3aed'
      }
      
      return {
        width: '60px',
        height: '60px',
        borderRadius: '50%',
        backgroundColor: 'white',
        color: colorMap[color] || '#6b7280',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '24px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)'
      }
    },
    
    getStatChangeStyle(trend) {
      const colorMap = {
        up: '#166534',
        down: '#991b1b',
        neutral: '#6b7280'
      }
      
      return {
        color: colorMap[trend] || '#6b7280',
        fontSize: '14px',
        fontWeight: '600',
        display: 'flex',
        alignItems: 'center',
        gap: '4px'
      }
    },
    
    getTaskStatusStyle(status) {
      const statusMap = {
        pendente: '#fbbf24',
        em_andamento: '#3b82f6',
        concluida: '#10b981'
      }
      
      return {
        width: '4px',
        height: '48px',
        backgroundColor: statusMap[status] || '#e5e7eb',
        borderRadius: '2px',
        marginRight: '16px'
      }
    },
    
    getTaskPriorityStyle(priority) {
      const priorityMap = {
        alta: { backgroundColor: '#fecaca', color: '#991b1b' },
        media: { backgroundColor: '#fde68a', color: '#92400e' },
        baixa: { backgroundColor: '#bfdbfe', color: '#1e40af' }
      }
      
      return {
        ...priorityMap[priority] || priorityMap.baixa,
        padding: '2px 8px',
        borderRadius: '12px',
        fontSize: '12px',
        fontWeight: '600',
        marginRight: '8px'
      }
    },
    
    getPriorityLabel(priority) {
      return {
        alta: 'Alta',
        media: 'Média',
        baixa: 'Baixa'
      }[priority] || 'Baixa'
    },
    
    getCompanyInitials(name) {
      if (!name) return '??'
      return name.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase()
    }
  },
  
  async mounted() {
    this.loading = true
    
    try {
      await Promise.all([
        this.fetchStats(),
        this.fetchRecentTasks(),
        this.fetchRecentCompanies()
      ])
    } catch (error) {
      console.error('Erro ao carregar dashboard:', error)
    } finally {
      this.loading = false
    }
  }
}
</script>

<style scoped>
.card-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.card-link {
  color: #667eea;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: color 0.3s ease;
}

.card-link:hover {
  color: #5a67d8;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 36px;
  font-weight: 800;
  color: #1f2937;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 16px;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 8px;
}

.task-info,
.company-info {
  flex: 1;
}

.task-title,
.company-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.task-meta,
.company-meta {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #6b7280;
}

.task-company {
  font-size: 13px;
}

.task-item:hover,
.company-item:hover {
  background-color: #f9fafb;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  color: #d1d5db;
}

.empty-state p {
  margin: 0 0 16px 0;
  font-size: 16px;
}

.create-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: '12px 20px',
  background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
  color: 'white',
  text-decoration: 'none',
  border-radius: '8px',
  font-size: '14px',
  font-weight: '600',
  transition: 'all 0.3s ease'
}

.create-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.quick-action-btn:hover {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

.quick-action-btn i {
  font-size: 16px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr !important;
  }
  
  .stat-card {
    padding: 20px !important;
  }
  
  .stat-value {
    font-size: 28px !important;
  }
  
  .quick-actions {
    gap: 8px !important;
  }
  
  .quick-action-btn {
    padding: 14px 16px !important;
  }
}
</style>
