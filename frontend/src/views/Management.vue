<template>
  <AppLayout
    page-title="Gerenciamento de Usuários"
    page-description="Gerencie usuários do sistema e suas empresas"
    :breadcrumbs="breadcrumbs"
  >
    <template v-slot:pageActions>
      <button
        @click="showCreateModal = true"
        class="btn btn-primary"
      >
        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Novo Usuário
      </button>
    </template>

    <!-- Loading Skeleton -->
    <div v-if="loading && !users.length" class="space-y-4">
      <div class="bg-white shadow rounded-lg p-6">
        <div class="animate-pulse">
          <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="h-10 bg-gray-200 rounded"></div>
            <div class="h-10 bg-gray-200 rounded"></div>
            <div class="h-10 bg-gray-200 rounded"></div>
            <div class="h-10 bg-gray-200 rounded"></div>
          </div>
        </div>
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <div class="animate-pulse space-y-4">
          <div v-for="i in 5" :key="i" class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
            <div class="w-20 h-8 bg-gray-200 rounded"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div v-else class="space-y-6">
      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="form-label">Buscar</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Nome ou email..."
              class="form-input"
              @input="debouncedSearch"
            >
          </div>
          
          <div>
            <label class="form-label">Role</label>
            <select v-model="filters.role" @change="loadUsers" class="form-select">
              <option value="">Todas</option>
              <option value="admin">Admin</option>
              <option value="user">Usuário</option>
            </select>
          </div>
          
          <div>
            <label class="form-label">Empresa</label>
            <select v-model="filters.company_id" @change="loadUsers" class="form-select">
              <option value="">Todas</option>
              <option
                v-for="company in companies"
                :key="company.id"
                :value="company.id"
              >
                {{ company.name }}
              </option>
            </select>
          </div>
          
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="btn btn-secondary w-full"
            >
              Limpar Filtros
            </button>
          </div>
        </div>
      </div>

      <!-- Users List -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            Usuários ({{ pagination.total || 0 }})
          </h3>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Usuário
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Empresa
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Role
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Criado em
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-semibold text-sm">
                        {{ getUserInitials(user.name) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ user.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ user.company?.name || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getRoleBadgeClass(user.role)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ getRoleLabel(user.role) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ user.created_at | formatDate }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <button
                      @click="editUser(user)"
                      class="text-indigo-600 hover:text-indigo-900 p-1 rounded"
                      title="Editar"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="confirmDelete(user)"
                      class="text-red-600 hover:text-red-900 p-1 rounded"
                      title="Deletar"
                      :disabled="user.id === currentUser?.id"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty State -->
          <div v-if="!loading && users.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum usuário encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ hasFilters ? 'Tente ajustar os filtros de busca.' : 'Comece criando um novo usuário.' }}
            </p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="px-6 py-3 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
              Mostrando {{ (pagination.current_page - 1) * pagination.per_page + 1 }} até 
              {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} de 
              {{ pagination.total }} resultados
            </div>
            <div class="flex space-x-2">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="btn btn-secondary btn-sm"
              >
                Anterior
              </button>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="btn btn-secondary btn-sm"
              >
                Próxima
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <UserModal
      :show="showCreateModal || showEditModal"
      :user="selectedUser"
      :companies="companies"
      @close="closeModal"
      @saved="handleUserSaved"
    />
  </AppLayout>
</template>

<script>
import { mapGetters } from 'vuex'
import AppLayout from '@/components/layout/AppLayout.vue'
import UserModal from '@/components/modals/UserModal.vue'

export default {
  name: 'Management',
  components: {
    AppLayout,
    UserModal
  },
  data() {
    return {
      loading: false,
      users: [],
      companies: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0
      },
      filters: {
        search: '',
        role: '',
        company_id: ''
      },
      showCreateModal: false,
      showEditModal: false,
      selectedUser: null,
      searchTimeout: null
    }
  },
  computed: {
    ...mapGetters('auth', ['user', 'isAdmin']),
    currentUser() {
      return this.user
    },
    breadcrumbs() {
      return [
        { label: 'Dashboard', path: '/' },
        { label: 'Gerenciamento de Usuários' }
      ]
    },
    hasFilters() {
      return this.filters.search || this.filters.role || this.filters.company_id
    }
  },
  async mounted() {
    // Verificar se é admin antes de carregar
    if (!this.isAdmin) {
      this.$router.replace('/')
      return
    }

    await Promise.all([
      this.loadUsers(),
      this.loadCompanies()
    ])
  },
  methods: {
    async loadUsers() {
      this.loading = true
      
      try {
        const params = {
          page: this.pagination.current_page,
          per_page: this.pagination.per_page,
          ...this.filters
        }
        
        // Remove empty values
        Object.keys(params).forEach(key => {
          if (params[key] === '' || params[key] === null || params[key] === undefined) {
            delete params[key]
          }
        })

        const response = await this.$http.get('/management/users', { params })
        
        this.users = response.data.data || []
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total
        }
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Não foi possível carregar os usuários.',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },

    async loadCompanies() {
      try {
        const response = await this.$http.get('/companies')
        this.companies = response.data.data || response.data || []
      } catch (error) {
        console.error('Erro ao carregar empresas:', error)
      }
    },

    debouncedSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1
        this.loadUsers()
      }, 500)
    },

    clearFilters() {
      this.filters = {
        search: '',
        role: '',
        company_id: ''
      }
      this.pagination.current_page = 1
      this.loadUsers()
    },

    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page
        this.loadUsers()
      }
    },

    editUser(user) {
      this.selectedUser = { ...user }
      this.showEditModal = true
    },

    closeModal() {
      this.showCreateModal = false
      this.showEditModal = false
      this.selectedUser = null
    },

    handleUserSaved() {
      this.closeModal()
      this.loadUsers()
      this.$swal.fire({
        title: 'Sucesso!',
        text: 'Usuário salvo com sucesso.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      })
    },

    async confirmDelete(user) {
      const result = await this.$swal.fire({
        title: 'Tem certeza?',
        text: `Deseja realmente deletar o usuário "${user.name}"? Esta ação não pode ser desfeita.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, deletar!',
        cancelButtonText: 'Cancelar'
      })

      if (result.isConfirmed) {
        await this.deleteUser(user)
      }
    },

    async deleteUser(user) {
      try {
        await this.$http.delete(`/management/users/${user.id}`)
        
        this.$swal.fire({
          title: 'Deletado!',
          text: 'Usuário deletado com sucesso.',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })

        // Reload users
        await this.loadUsers()
      } catch (error) {
        console.error('Erro ao deletar usuário:', error)
        this.$swal.fire({
          title: 'Erro',
          text: error.response?.data?.message || 'Não foi possível deletar o usuário.',
          icon: 'error'
        })
      }
    },

    getUserInitials(name) {
      if (!name) return 'U'
      return name.split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    },

    getRoleLabel(role) {
      const labels = {
        admin: 'Admin',
        user: 'Usuário'
      }
      return labels[role] || role
    },

    getRoleBadgeClass(role) {
      const classes = {
        admin: 'bg-red-100 text-red-800',
        user: 'bg-blue-100 text-blue-800'
      }
      return classes[role] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script>
