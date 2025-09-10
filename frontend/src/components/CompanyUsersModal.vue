<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white max-h-screen-90 overflow-y-auto">
      <div class="flex items-center justify-between pb-3 border-b border-gray-200">
        <div>
          <h3 class="text-lg font-bold text-gray-900">
            Usuários da Empresa: {{ company.name }}
          </h3>
          <p class="text-sm text-gray-600">
            {{ users.length }} usuário{{ users.length !== 1 ? 's' : '' }} encontrado{{ users.length !== 1 ? 's' : '' }}
          </p>
        </div>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="mt-4">
        <!-- Search -->
        <div class="mb-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar usuários..."
            class="form-input"
          >
        </div>

        <div v-if="loading" class="flex justify-center py-8">
          <div class="spinner-lg"></div>
        </div>

        <div v-else-if="filteredUsers.length === 0" class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum usuário encontrado</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ searchQuery ? 'Tente ajustar a busca.' : 'Esta empresa ainda não possui usuários.' }}
          </p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-primary-600 text-sm font-medium">
                  {{ getUserInitials(user.name) }}
                </span>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-900">{{ user.name }}</h4>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
                <div class="flex items-center space-x-3 mt-1">
                  <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                    :class="getRoleClass(user.role)"
                  >
                    {{ getRoleName(user.role) }}
                  </span>
                  <span class="text-xs text-gray-400">
                    Criado em {{ user.created_at | formatDate }}
                  </span>
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <div class="text-right">
                <p class="text-sm font-medium text-gray-900">
                  {{ user.tasks_count || 0 }} tarefa{{ (user.tasks_count || 0) !== 1 ? 's' : '' }}
                </p>
                <p class="text-xs text-gray-500">
                  Último acesso: {{ user.last_login_at ? (user.last_login_at | formatDate) : 'Nunca' }}
                </p>
              </div>
              
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="user.status === 'active' 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-red-100 text-red-800'"
              >
                {{ user.status === 'active' ? 'Ativo' : 'Inativo' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end pt-6 mt-6 border-t border-gray-200">
        <button
          @click="$emit('close')"
          class="btn btn-secondary"
        >
          Fechar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CompanyUsersModal',
  props: {
    company: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      users: [],
      loading: false,
      searchQuery: ''
    }
  },
  computed: {
    filteredUsers() {
      if (!this.searchQuery) return this.users
      
      const query = this.searchQuery.toLowerCase()
      return this.users.filter(user =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query)
      )
    }
  },
  async created() {
    await this.loadUsers()
  },
  methods: {
    async loadUsers() {
      this.loading = true
      
      try {
        const response = await this.$http.get(`/companies/${this.company.id}/users`)
        this.users = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Não foi possível carregar os usuários da empresa.',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },

    getUserInitials(name) {
      if (!name) return ''
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substr(0, 2)
        .toUpperCase()
    },

    getRoleName(role) {
      const roles = {
        admin: 'Administrador',
        manager: 'Gerente',
        user: 'Usuário'
      }
      return roles[role] || role
    },

    getRoleClass(role) {
      const classes = {
        admin: 'bg-purple-100 text-purple-800',
        manager: 'bg-blue-100 text-blue-800',
        user: 'bg-gray-100 text-gray-800'
      }
      return classes[role] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script>

<style scoped>
.max-h-screen-90 {
  max-height: 90vh;
}
</style>
