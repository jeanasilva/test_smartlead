<template>
  <AppLayout
    page-title="Dashboard"
    page-description="Visão geral do seu sistema de tarefas"
    :breadcrumbs="breadcrumbs"
  >
    <template v-slot:pageActions>
      <button @click="refreshData" class="refresh-btn" :style="refreshBtnStyle">
        <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
        Atualizar
      </button>
    </template>

    <!-- Stats Cards -->
    <div class="stats-grid" :style="statsGridStyle">
      <SkeletonLoader 
        v-if="loading && !statsLoaded" 
        type="stats" 
        :items="4" 
      />
      <div v-else class="stats-cards">
        <div 
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
            <div class="stat-change" :class="stat.trend">
              <i :class="stat.trendIcon"></i>
              {{ stat.change }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid" :style="dashboardGridStyle">
      <!-- Recent Tasks -->
      <div class="dashboard-card" :style="cardStyle">
        <div class="card-header" :style="cardHeaderStyle">
          <h3 class="card-title">
            <i class="fas fa-tasks"></i>
            Tarefas Recentes
          </h3>
          <router-link to="/tasks" class="view-all-link">Ver todas</router-link>
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
              <div class="task-info">
                <div class="task-title">{{ task.title }}</div>
                <div class="task-meta">
                  <span class="task-company">{{ task.company?.name }}</span>
                  <span class="task-date">{{ formatDate(task.created_at) }}</span>
                </div>
              </div>
              <div class="task-status" :style="getTaskStatusStyle(task.status)">
                {{ getStatusLabel(task.status) }}
              </div>
            </div>
          </div>
          <div v-else class="empty-state" :style="emptyStateStyle">
            <i class="fas fa-tasks"></i>
            <p>Nenhuma tarefa encontrada</p>
            <router-link to="/tasks" class="create-btn">Criar primeira tarefa</router-link>
          </div>
        </div>
      </div>

      <!-- Activity Chart -->
      <div class="dashboard-card" :style="cardStyle">
        <div class="card-header" :style="cardHeaderStyle">
          <h3 class="card-title">
            <i class="fas fa-chart-line"></i>
            Atividade dos Últimos 7 Dias
          </h3>
        </div>
        <div class="card-body">
          <SkeletonLoader 
            v-if="loading && !chartLoaded" 
            type="chart" 
          />
          <div v-else class="chart-container" :style="chartContainerStyle">
            <div class="chart-bars" :style="chartBarsStyle">
              <div 
                v-for="(day, index) in chartData" 
                :key="index"
                class="chart-bar"
                :style="getChartBarStyle(day.value, index)"
                :title="`${day.label}: ${day.value} tarefas`"
              >
                <div class="bar-value">{{ day.value }}</div>
              </div>
            </div>
            <div class="chart-labels" :style="chartLabelsStyle">
              <span v-for="day in chartData" :key="day.label" class="chart-label">
                {{ day.label }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Companies Overview -->
      <div class="dashboard-card" :style="cardStyle">
        <div class="card-header" :style="cardHeaderStyle">
          <h3 class="card-title">
            <i class="fas fa-building"></i>
            Empresas Ativas
          </h3>
          <router-link to="/companies" class="view-all-link">Ver todas</router-link>
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
              @click="$router.push('/tasks?action=create')"
              class="quick-action-btn"
              :style="quickActionBtnStyle"
            >
              <i class="fas fa-plus"></i>
              <span>Nova Tarefa</span>
            </button>
            <button 
              @click="$router.push('/companies?action=create')"
              class="quick-action-btn"
              :style="quickActionBtnStyle"
            >
              <i class="fas fa-building"></i>
              <span>Nova Empresa</span>
            </button>
            <button 
              @click="$router.push('/reports')"
              class="quick-action-btn"
              :style="quickActionBtnStyle"
            >
              <i class="fas fa-chart-bar"></i>
              <span>Relatórios</span>
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
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: '16px 0',
        borderBottom: '1px solid #f3f4f6'
      }
    },
    companyItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        padding: '12px 0',
        borderBottom: '1px solid #f3f4f6'
      }
    },
    companyAvatarStyle() {
      return {
        width: '40px',
        height: '40px',
        borderRadius: '50%',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: 'white',
        fontSize: '14px',
        fontWeight: '600',
        marginRight: '12px'
      }
    },
    emptyStateStyle() {
      return {
        textAlign: 'center',
        padding: '40px 20px',
        color: '#6b7280'
      }
    },
    chartContainerStyle() {
      return {
        height: '200px',
        display: 'flex',
        flexDirection: 'column'
      }
    },
    chartBarsStyle() {
      return {
        display: 'flex',
        alignItems: 'flex-end',
        justifyContent: 'space-around',
        height: '150px',
        marginBottom: '16px'
      }
    },
    chartLabelsStyle() {
      return {
        display: 'flex',
        justifyContent: 'space-around',
        fontSize: '12px',
        color: '#6b7280'
      }
    },
    quickActionsStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(2, 1fr)',
        gap: '12px'
      }
    },
    quickActionBtnStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: '8px',
        padding: '20px',
        background: '#f9fafb',
        border: '1px solid #e5e7eb',
        borderRadius: '12px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        fontSize: '14px',
        fontWeight: '500',
        color: '#374151'
      }
    },
    refreshBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '8px 16px',
        background: 'white',
        border: '1px solid #d1d5db',
        borderRadius: '8px',
        cursor: 'pointer',
        fontSize: '14px',
        fontWeight: '500',
        color: '#374151',
        transition: 'all 0.3s ease'
      }
    }
  },
  methods: {
    async fetchDashboardData() {
      this.loading = true
      try {
        await Promise.all([
          this.fetchStats(),
          this.fetchRecentTasks(),
          this.fetchRecentCompanies(),
          this.fetchChartData()
        ])
      } catch (error) {
        console.error('Erro ao carregar dashboard:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Erro ao carregar dados do dashboard',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },
    
    async fetchStats() {
      try {
        const response = await dashboardService.stats()
        const data = response.data
        
        // Os dados vêm dentro de data.stats
        const stats = data.stats || data
        
        console.log('Dashboard Stats:', stats) // Para debug
        
        this.stats = [
          {
            id: 1,
            label: 'Total de Tarefas',
            value: stats.total_tasks || '0',
            change: '+12%',
            trend: 'up',
            trendIcon: 'fas fa-arrow-up',
            icon: 'fas fa-tasks',
            color: 'blue'
          },
          {
            id: 2,
            label: 'Concluídas',
            value: stats.completed_tasks || '0',
            change: '+8%',
            trend: 'up',
            trendIcon: 'fas fa-arrow-up',
            icon: 'fas fa-check-circle',
            color: 'green'
          },
          {
            id: 3,
            label: 'Pendentes',
            value: stats.pending_tasks || '0',
            change: '-2%',
            trend: 'down',
            trendIcon: 'fas fa-arrow-down',
            icon: 'fas fa-clock',
            color: 'yellow'
          },
          {
            id: 4,
            label: 'Em Andamento',
            value: stats.in_progress_tasks || '0',
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
        console.error('Erro detalhado:', error.response?.data) // Para debug
        
        // Fallback com dados simulados
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
          },
          {
            id: 2,
            label: 'Concluídas',
            value: '0',
            change: '0%',
            trend: 'neutral', 
            trendIcon: 'fas fa-minus',
            icon: 'fas fa-check-circle',
            color: 'green'
          },
          {
            id: 3,
            label: 'Pendentes',
            value: '0',
            change: '0%',
            trend: 'neutral',
            trendIcon: 'fas fa-minus',
            icon: 'fas fa-clock',
            color: 'yellow'
          },
          {
            id: 4,
            label: 'Em Andamento',
            value: '0',
            change: '0%',
            trend: 'neutral',
            trendIcon: 'fas fa-minus',
            icon: 'fas fa-play-circle',
            color: 'purple'
          }
        ]
        this.statsLoaded = true
      }
    },
    
    async fetchRecentTasks() {
      try {
        const response = await dashboardService.recentTasks()
        console.log('Recent Tasks Response:', response.data) // Para debug
        
        // Verifica se os dados estão em data.data ou diretamente em data
        this.recentTasks = response.data.data || response.data || []
        this.tasksLoaded = true
      } catch (error) {
        console.error('Erro ao buscar tarefas recentes:', error)
        this.recentTasks = []
        this.tasksLoaded = true
      }
    },
    
    async fetchRecentCompanies() {
      try {
        const response = await companyService.list({ limit: 3 })
        this.recentCompanies = response.data.data || []
        this.companiesLoaded = true
      } catch (error) {
        console.error('Erro ao buscar empresas:', error)
        this.recentCompanies = []
        this.companiesLoaded = true
      }
    },
    
    async fetchChartData() {
      try {
        await new Promise(resolve => setTimeout(resolve, 1200))
        
        this.chartData = [
          { label: 'Dom', value: 3 },
          { label: 'Seg', value: 7 },
          { label: 'Ter', value: 5 },
          { label: 'Qua', value: 9 },
          { label: 'Qui', value: 4 },
          { label: 'Sex', value: 8 },
          { label: 'Sáb', value: 2 }
        ]
        
        this.chartLoaded = true
      } catch (error) {
        console.error('Erro ao buscar dados do gráfico:', error)
      }
    },
    
    async refreshData() {
      this.statsLoaded = false
      this.tasksLoaded = false
      this.companiesLoaded = false
      this.chartLoaded = false
      await this.fetchDashboardData()
    },
    
    getStatCardStyle(color) {
      const colors = {
        blue: { bg: '#dbeafe', border: '#3b82f6' },
        green: { bg: '#dcfce7', border: '#10b981' },
        yellow: { bg: '#fef3c7', border: '#f59e0b' },
        purple: { bg: '#ede9fe', border: '#8b5cf6' }
      }
      
      return {
        background: 'white',
        borderRadius: '16px',
        padding: '24px',
        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
        border: `1px solid ${colors[color]?.border || '#e5e7eb'}`,
        display: 'flex',
        alignItems: 'center',
        gap: '16px',
        transition: 'transform 0.3s ease',
        cursor: 'pointer'
      }
    },
    
    getStatIconStyle(color) {
      const colors = {
        blue: '#3b82f6',
        green: '#10b981',
        yellow: '#f59e0b',
        purple: '#8b5cf6'
      }
      
      return {
        width: '56px',
        height: '56px',
        borderRadius: '12px',
        background: `linear-gradient(135deg, ${colors[color]} 0%, ${colors[color]}aa 100%)`,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: 'white',
        fontSize: '24px'
      }
    },
    
    getTaskStatusStyle(status) {
      const styles = {
        pending: {
          background: '#fef3c7',
          color: '#92400e',
          padding: '4px 12px',
          borderRadius: '20px',
          fontSize: '12px',
          fontWeight: '600'
        },
        in_progress: {
          background: '#dbeafe',
          color: '#1e40af',
          padding: '4px 12px',
          borderRadius: '20px',
          fontSize: '12px',
          fontWeight: '600'
        },
        completed: {
          background: '#dcfce7',
          color: '#166534',
          padding: '4px 12px',
          borderRadius: '20px',
          fontSize: '12px',
          fontWeight: '600'
        }
      }
      
      return styles[status] || styles.pending
    },
    
    getChartBarStyle(value, index) {
      const maxValue = Math.max(...this.chartData.map(d => d.value))
      const height = Math.max((value / maxValue) * 100, 10)
      
      return {
        width: '32px',
        height: `${height}px`,
        background: `linear-gradient(135deg, #667eea ${index * 10}%, #764ba2 ${100 - index * 10}%)`,
        borderRadius: '4px 4px 0 0',
        position: 'relative',
        display: 'flex',
        alignItems: 'flex-start',
        justifyContent: 'center',
        paddingTop: '4px',
        color: 'white',
        fontSize: '11px',
        fontWeight: '600'
      }
    },
    
    formatDate(date) {
      return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit'
      })
    },
    
    getStatusLabel(status) {
      const labels = {
        pending: 'Pendente',
        in_progress: 'Em Andamento',
        completed: 'Concluída'
      }
      return labels[status] || 'Pendente'
    },
    
    getCompanyInitials(name) {
      return name.split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }
  },
  
  async mounted() {
    await this.fetchDashboardData()
  }
}
</script>

<style scoped>
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 8px;
}

.stat-change {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
}

.stat-change.up {
  color: #10b981;
}

.stat-change.down {
  color: #ef4444;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.view-all-link {
  color: #667eea;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.view-all-link:hover {
  color: #764ba2;
}

.card-body {
  padding: 0 24px 24px;
}

.task-title {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.task-meta {
  display: flex;
  gap: 16px;
  font-size: 13px;
  color: #6b7280;
}

.company-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.company-meta {
  font-size: 13px;
  color: #6b7280;
  margin-top: 2px;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
}

.create-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  margin-top: 16px;
  transition: all 0.3s ease;
}

.quick-action-btn:hover {
  background: #f3f4f6 !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.refresh-btn:hover {
  background: #f9fafb !important;
  border-color: #9ca3af !important;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .dashboard-grid {
    grid-template-columns: 1fr !important;
  }
  
  .stats-cards {
    grid-template-columns: 1fr !important;
  }
  
  .quick-actions {
    grid-template-columns: 1fr !important;
  }
}
</style>
