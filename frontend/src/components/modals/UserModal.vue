<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

    <!-- Modal panel -->
    <div class="relative bg-white rounded-lg px-6 pt-6 pb-6 text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl">
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ isEdit ? 'Editar Usuário' : 'Novo Usuário' }}
            </h3>
            
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <!-- Nome e Email - Lado a lado -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="name" class="form-label">Nome *</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="form-input"
                    :class="{ 'border-red-500': errors.name }"
                  >
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
                </div>

                <div>
                  <label for="email" class="form-label">Email *</label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="form-input"
                    :class="{ 'border-red-500': errors.email }"
                  >
                  <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
                </div>
              </div>

              <!-- Password Fields - Create Mode -->
              <div v-if="!isEdit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="password" class="form-label">Senha *</label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    class="form-input"
                    :class="{ 'border-red-500': errors.password }"
                  >
                  <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
                </div>

                <div>
                  <label for="password_confirmation" class="form-label">Confirmar Senha *</label>
                  <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    class="form-input"
                  >
                </div>
              </div>

              <!-- Change Password Checkbox (Edit Mode) -->
              <div v-if="isEdit" class="flex items-center">
                <label class="flex items-center cursor-pointer">
                  <input
                    v-model="changePassword"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  >
                  <span class="ml-2 text-sm text-gray-700">Alterar senha</span>
                </label>
              </div>

              <!-- New Password Fields (Edit Mode) -->
              <div v-if="isEdit && changePassword" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="new_password" class="form-label">Nova Senha *</label>
                  <input
                    id="new_password"
                    v-model="form.password"
                    type="password"
                    class="form-input"
                    :class="{ 'border-red-500': errors.password }"
                  >
                  <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
                </div>

                <div>
                  <label for="new_password_confirmation" class="form-label">Confirmar Nova Senha *</label>
                  <input
                    id="new_password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="form-input"
                  >
                </div>
              </div>

              <!-- Role e Company - Lado a lado -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="role" class="form-label">Função *</label>
                  <select
                    id="role"
                    v-model="form.role"
                    class="form-select"
                    :class="{ 'border-red-500': errors.role }"
                  >
                    <option value="user">Usuário</option>
                    <option value="admin">Administrador</option>
                  </select>
                  <p v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role[0] }}</p>
                </div>

                <div>
                  <label for="company_id" class="form-label">Empresa *</label>
                  <select
                    id="company_id"
                    v-model="form.company_id"
                    class="form-select"
                    :class="{ 'border-red-500': errors.company_id }"
                  >
                    <option value="">Selecione uma empresa</option>
                    <option
                      v-for="company in companies"
                      :key="company.id"
                      :value="company.id"
                    >
                      {{ company.name }}
                    </option>
                  </select>
                  <p v-if="errors.company_id" class="mt-1 text-sm text-red-600">{{ errors.company_id[0] }}</p>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="btn btn-secondary"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            @click="handleSubmit"
            :disabled="loading || !isFormValid"
            class="btn btn-primary"
          >
            <span v-if="loading" class="spinner mr-2"></span>
            {{ isEdit ? 'Salvar' : 'Criar Usuário' }}
          </button>
        </div>
      </div>
  </div>
</template>

<script>
export default {
  name: 'UserModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    user: {
      type: Object,
      default: null
    },
    companies: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      loading: false,
      changePassword: false,
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'user',
        company_id: ''
      },
      errors: {}
    }
  },
  computed: {
    isEdit() {
      return this.user && this.user.id
    },
    isFormValid() {
      const requiredFields = ['name', 'email', 'role', 'company_id']
      
      if (!this.isEdit || this.changePassword) {
        requiredFields.push('password', 'password_confirmation')
      }
      
      return requiredFields.every(field => this.form[field]) &&
        (!this.form.password || this.form.password === this.form.password_confirmation)
    }
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.resetForm()
        if (this.isEdit) {
          this.populateForm()
        }
      }
    },
    user: {
      handler() {
        if (this.show && this.isEdit) {
          this.populateForm()
        }
      },
      immediate: true
    }
  },
  methods: {
    resetForm() {
      this.form = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'user',
        company_id: ''
      }
      this.errors = {}
      this.changePassword = false
    },

    populateForm() {
      if (this.user) {
        this.form = {
          name: this.user.name || '',
          email: this.user.email || '',
          password: '',
          password_confirmation: '',
          role: this.user.role || 'user',
          company_id: this.user.company_id || this.user.company?.id || ''
        }
      }
    },

    async handleSubmit() {
      if (!this.isFormValid) return

      this.loading = true
      this.errors = {}

      try {
        const data = {
          name: this.form.name,
          email: this.form.email,
          role: this.form.role,
          company_id: this.form.company_id
        }

        // Add password fields if needed
        if (!this.isEdit || this.changePassword) {
          data.password = this.form.password
          data.password_confirmation = this.form.password_confirmation
        }

        let response

        if (this.isEdit) {
          response = await this.$http.put(`/management/users/${this.user.id}`, data)
        } else {
          response = await this.$http.post('/management/users', data)
        }

        this.$emit('saved', response.data)
      } catch (error) {
        console.error('Erro ao salvar usuário:', error)
        
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {}
        } else {
          this.$swal.fire({
            title: 'Erro',
            text: error.response?.data?.message || 'Não foi possível salvar o usuário.',
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

<style scoped>
.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Melhorias no modal */
.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.25rem;
}

.form-input, .form-select {
  margin-top: 0.25rem;
  display: block;
  width: 100%;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
</style>
