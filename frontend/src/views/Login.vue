<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="max-w-md w-full space-y-8">
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center">
          <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="mt-6 text-3xl font-bold text-gray-900">
            Acesse sua conta
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Entre no sistema de gerenciamento de tarefas
          </p>
        </div>
        
        <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
          <div class="space-y-5">
            <div>
              <label for="email" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                E-mail
              </label>
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                required
                style="display: block; width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; background-color: #ffffff; font-size: 0.875rem; line-height: 1.25rem; color: #374151; transition: all 0.2s ease-in-out;"
                :style="{ 'border-color': errors.email ? '#ef4444' : '#e5e7eb' }"
                placeholder="seu@email.com"
                @focus="$event.target.style.borderColor = '#3b82f6'; $event.target.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)'"
                @blur="$event.target.style.borderColor = errors.email ? '#ef4444' : '#e5e7eb'; $event.target.style.boxShadow = 'none'"
              >
              <p v-if="errors.email" style="margin-top: 0.25rem; font-size: 0.875rem; color: #ef4444;">
                {{ errors.email }}
              </p>
            </div>
            
            <div>
              <label for="password" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                Senha
              </label>
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                required
                style="display: block; width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; background-color: #ffffff; font-size: 0.875rem; line-height: 1.25rem; color: #374151; transition: all 0.2s ease-in-out;"
                :style="{ 'border-color': errors.password ? '#ef4444' : '#e5e7eb' }"
                placeholder="Sua senha"
                @focus="$event.target.style.borderColor = '#3b82f6'; $event.target.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)'"
                @blur="$event.target.style.borderColor = errors.password ? '#ef4444' : '#e5e7eb'; $event.target.style.boxShadow = 'none'"
              >
              <p v-if="errors.password" style="margin-top: 0.25rem; font-size: 0.875rem; color: #ef4444;">
                {{ errors.password }}
              </p>
            </div>
          </div>
          
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember"
                v-model="form.remember"
                name="remember"
                type="checkbox"
                style="height: 1rem; width: 1rem; color: #3b82f6; border-color: #d1d5db; border-radius: 0.25rem;"
              >
              <label for="remember" style="margin-left: 0.5rem; display: block; font-size: 0.875rem; color: #374151;">
                Lembrar de mim
              </label>
            </div>
          </div>
          
          <div>
            <button
              type="submit"
              :disabled="loading"
              style="position: relative; display: flex; justify-content: center; align-items: center; width: 100%; padding: 0.75rem 1rem; border: none; font-size: 0.875rem; font-weight: 600; border-radius: 0.75rem; color: #ffffff; cursor: pointer; transition: all 0.2s ease-in-out; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);"
              :style="{ opacity: loading ? '0.7' : '1', cursor: loading ? 'not-allowed' : 'pointer' }"
              @mouseover="onButtonHover"
              @mouseout="onButtonLeave"
            >
              <span v-if="loading" class="spinner" style="margin-right: 0.5rem;"></span>
              {{ loading ? 'Entrando...' : 'Entrar' }}
            </button>
          </div>
          
          <div class="text-center">
            <router-link
              to="/register"
              style="color: #3b82f6; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: color 0.2s ease-in-out;"
              @mouseover="$event.target.style.color = '#1d4ed8'"
              @mouseout="$event.target.style.color = '#3b82f6'"
            >
              Não tem uma conta? Cadastre-se aqui
            </router-link>
          </div>
          
          <!-- Credenciais de teste -->
          <div style="margin-top: 2rem; padding: 1rem; background-color: #f3f4f6; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
            <h4 style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Credenciais de Teste:</h4>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0;">Email: teste@smartlead.com</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0;">Senha: password123</p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      },
      errors: {}
    }
  },
  computed: {
    ...mapGetters('auth', ['loading'])
  },
  methods: {
    ...mapActions('auth', ['login']),
    
    onButtonHover(event) {
      if (!this.loading) {
        event.target.style.transform = 'translateY(-1px)'
        event.target.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)'
      }
    },
    
    onButtonLeave(event) {
      event.target.style.transform = 'translateY(0)'
      event.target.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
    },
    
    async handleLogin() {
      this.errors = {}
      
      try {
        await this.login(this.form)
        
        this.$swal.fire({
          title: 'Login realizado!',
          text: 'Bem-vindo(a) ao SmartLead Tasks',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
        
        this.$router.push('/')
      } catch (error) {
        console.error('Erro no login:', error)
        
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          this.$swal.fire({
            title: 'Erro no login',
            text: error.response?.data?.message || 'Verifique suas credenciais e tente novamente.',
            icon: 'error'
          })
        }
      }
    }
  }
}
</script>
