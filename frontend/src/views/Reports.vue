<template>
  <AppLayout
    page-title="Relatórios"
    page-description="Análise de dados e exportação de relatórios"
    :breadcrumbs="breadcrumbs"
  >
    <template v-slot:pageActions>
      <button
        @click="generateReport"
        :disabled="loading"
        class="btn btn-primary"
      >
        <svg v-if="!loading" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span v-if="loading" class="spinner mr-2"></span>
        {{ loading ? 'Gerando...' : 'Exportar PDF' }}
      </button>
      <button
        @click="exportExcel"
        :disabled="loading"
        class="btn btn-success"
      >
        <svg v-if="!loading" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        <span v-if="loading" class="spinner mr-2"></span>
        {{ loading ? 'Exportando...' : 'Exportar Excel' }}
      </button>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros do Relatório</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="form-label">Data inicial</label>
            <input
              v-model="filters.start_date"
              type="date"
              class="form-input"
            >
          </div>
          
          <div>
            <label class="form-label">Data final</label>
            <input
              v-model="filters.end_date"
              type="date"
              class="form-input"
            >
          </div>
          
          <div>
            <label class="form-label">Status</label>
            <select v-model="filters.status" class="form-select">
              <option value="">Todos</option>
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Progresso</option>
              <option value="completed">Concluída</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>
          
          <div>
            <label class="form-label">Usuário</label>
            <select v-model="filters.user_id" class="form-select">
              <option value="">Todos</option>
              <option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
              >
                {{ user.name }}
              </option>
            </select>
          </div>
        </div>
        
        <div class="flex justify-end mt-4">
          <button
            @click="loadReportData"
            class="btn btn-primary"
          >
            Aplicar Filtros
          </button>
        </div>
      </div>

      <!-- Statistics Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total de Tarefas</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ reportStats.total_tasks }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Concluídas</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ reportStats.completed_tasks }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Em Progresso</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ reportStats.in_progress_tasks }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-primary-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Taxa de Conclusão</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ completionRate }}%</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tasks by Status -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Tarefas por Status</h3>
          <div class="space-y-3">
            <div
              v-for="(count, status) in statusDistribution"
              :key="status"
              class="flex items-center justify-between"
            >
              <div class="flex items-center space-x-3">
                <div
                  class="w-3 h-3 rounded-full"
                  :class="getStatusColor(status)"
                ></div>
                <span class="text-sm text-gray-600">{{ getStatusName(status) }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm font-medium text-gray-900">{{ count }}</span>
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div
                    class="h-2 rounded-full"
                    :class="getStatusBgColor(status)"
                    :style="{ width: `${getPercentage(count, reportStats.total_tasks)}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tasks by Priority -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Tarefas por Prioridade</h3>
          <div class="space-y-3">
            <div
              v-for="(count, priority) in priorityDistribution"
              :key="priority"
              class="flex items-center justify-between"
            >
              <div class="flex items-center space-x-3">
                <div
                  class="w-3 h-3 rounded-full"
                  :class="getPriorityColor(priority)"
                ></div>
                <span class="text-sm text-gray-600">{{ getPriorityName(priority) }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm font-medium text-gray-900">{{ count }}</span>
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div
                    class="h-2 rounded-full"
                    :class="getPriorityBgColor(priority)"
                    :style="{ width: `${getPercentage(count, reportStats.total_tasks)}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Users -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Usuários mais Produtivos
          </h3>
        </div>
        
        <div v-if="topUsers.length === 0" class="px-6 py-8 text-center">
          <p class="text-gray-500">Nenhum dado disponível no período selecionado.</p>
        </div>
        
        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="(userData, index) in topUsers"
            :key="userData.user.id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 text-sm font-medium">
                    {{ index + 1 }}
                  </span>
                </div>
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 text-sm font-medium">
                      {{ userData.user.name | getInitials }}
                    </span>
                  </div>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">{{ userData.user.name }}</h4>
                  <p class="text-sm text-gray-500">{{ userData.user.email }}</p>
                </div>
              </div>
              
              <div class="text-right">
                <div class="flex items-center space-x-6">
                  <div class="text-center">
                    <p class="text-lg font-semibold text-gray-900">{{ userData.total_tasks }}</p>
                    <p class="text-xs text-gray-500">Total</p>
                  </div>
                  <div class="text-center">
                    <p class="text-lg font-semibold text-green-600">{{ userData.completed_tasks }}</p>
                    <p class="text-xs text-gray-500">Concluídas</p>
                  </div>
                  <div class="text-center">
                    <p class="text-lg font-semibold text-primary-600">{{ userData.completion_rate }}%</p>
                    <p class="text-xs text-gray-500">Taxa</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/components/layout/AppLayout.vue'

export default {
  name: 'Reports',
  components: {
    AppLayout
  },
  data() {
    return {
      loading: false,
      breadcrumbs: [
        { name: 'Dashboard', path: '/dashboard' },
        { name: 'Relatórios', path: '/reports' }
      ],
      filters: {
        start_date: '',
        end_date: '',
        status: '',
        user_id: ''
      },
      users: [],
      reportStats: {
        total_tasks: 0,
        completed_tasks: 0,
        in_progress_tasks: 0,
        pending_tasks: 0,
        cancelled_tasks: 0
      },
      statusDistribution: {},
      priorityDistribution: {},
      topUsers: []
    }
  },
  computed: {
    completionRate() {
      if (this.reportStats.total_tasks === 0) return 0
      return Math.round((this.reportStats.completed_tasks / this.reportStats.total_tasks) * 100)
    }
  },
  async created() {
    // Definir datas padrão (último mês)
    const today = new Date()
    const lastMonth = new Date()
    lastMonth.setMonth(lastMonth.getMonth() - 1)
    
    this.filters.end_date = today.toISOString().split('T')[0]
    this.filters.start_date = lastMonth.toISOString().split('T')[0]
    
    await this.loadUsers()
    await this.loadReportData()
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

    async loadReportData() {
      this.loading = true
      
      try {
        const params = { ...this.filters }
        const response = await this.$http.get('/dashboard/stats', { params })
        
        const data = response.data
        this.reportStats = data.stats || {}
        this.statusDistribution = data.status_distribution || {}
        this.priorityDistribution = data.priority_distribution || {}
        this.topUsers = data.top_users || []
      } catch (error) {
        console.error('Erro ao carregar relatório:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Não foi possível carregar os dados do relatório.',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },

    async generateReport() {
      this.loading = true
      
      try {
        const params = { ...this.filters, format: 'pdf' }
        const response = await this.$http.get('/export/report/summary', {
          params,
          responseType: 'blob'
        })
        
        // Criar link para download
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `relatorio-tarefas-${new Date().toISOString().split('T')[0]}.pdf`)
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        
        this.$swal.fire({
          title: 'Relatório gerado!',
          text: 'O download do relatório foi iniciado.',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
      } catch (error) {
        console.error('Erro ao gerar relatório:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Não foi possível gerar o relatório PDF.',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },

    async exportExcel() {
      this.loading = true
      
      try {
        const params = { ...this.filters, format: 'excel' }
        const response = await this.$http.get('/export/tasks/csv', {
          params,
          responseType: 'blob'
        })
        
        // Criar link para download
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `relatorio-tarefas-${new Date().toISOString().split('T')[0]}.xlsx`)
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        
        this.$swal.fire({
          title: 'Relatório exportado!',
          text: 'O download da planilha foi iniciado.',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
      } catch (error) {
        console.error('Erro ao exportar Excel:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Não foi possível exportar a planilha.',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },

    getPercentage(value, total) {
      if (total === 0) return 0
      return Math.round((value / total) * 100)
    },

    getStatusName(status) {
      const names = {
        pending: 'Pendente',
        in_progress: 'Em Progresso',
        completed: 'Concluída',
        cancelled: 'Cancelada'
      }
      return names[status] || status
    },

    getStatusColor(status) {
      const colors = {
        pending: 'bg-yellow-400',
        in_progress: 'bg-blue-400',
        completed: 'bg-green-400',
        cancelled: 'bg-red-400'
      }
      return colors[status] || 'bg-gray-400'
    },

    getStatusBgColor(status) {
      const colors = {
        pending: 'bg-yellow-400',
        in_progress: 'bg-blue-400',
        completed: 'bg-green-400',
        cancelled: 'bg-red-400'
      }
      return colors[status] || 'bg-gray-400'
    },

    getPriorityName(priority) {
      const names = {
        low: 'Baixa',
        medium: 'Média',
        high: 'Alta'
      }
      return names[priority] || priority
    },

    getPriorityColor(priority) {
      const colors = {
        low: 'bg-green-400',
        medium: 'bg-yellow-400',
        high: 'bg-red-400'
      }
      return colors[priority] || 'bg-gray-400'
    },

    getPriorityBgColor(priority) {
      const colors = {
        low: 'bg-green-400',
        medium: 'bg-yellow-400',
        high: 'bg-red-400'
      }
      return colors[priority] || 'bg-gray-400'
    }
  },
  filters: {
    getInitials(name) {
      if (!name) return ''
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substr(0, 2)
        .toUpperCase()
    }
  }
}
</script>
