<template>
  <div class="register-page" :style="pageStyle">
    <div class="register-container" :style="containerStyle">
      <!-- Background Elements -->
      <div class="background-elements">
        <div class="bg-circle bg-circle-1" :style="circle1Style"></div>
        <div class="bg-circle bg-circle-2" :style="circle2Style"></div>
        <div class="bg-circle bg-circle-3" :style="circle3Style"></div>
      </div>

      <!-- Register Card -->
      <div class="register-card" :style="cardStyle">
        <!-- Header -->
        <div class="register-header" :style="headerStyle">
          <div class="logo-section" :style="logoSectionStyle">
            <div class="logo" :style="logoStyle">
              <svg width="40" height="40" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="url(#registerGradient)" />
                <path d="M8 16L14 22L24 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                  <linearGradient id="registerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#667eea"/>
                    <stop offset="100%" stop-color="#764ba2"/>
                  </linearGradient>
                </defs>
              </svg>
            </div>
            <div class="brand-text">
              <h1 class="brand-title">SmartLead</h1>
              <span class="brand-subtitle">Tasks</span>
            </div>
          </div>
          
          <div class="header-content">
            <h2 class="register-title">Criar nova conta</h2>
            <p class="register-subtitle">
              Já tem uma conta? 
              <router-link to="/login" class="login-link">Faça login aqui</router-link>
            </p>
          </div>
        </div>

        <!-- Register Form -->
        <form @submit.prevent="handleRegister" class="register-form">
          <!-- Name Field -->
          <div class="form-group">
            <label for="name" class="form-label">
              <i class="fas fa-user"></i>
              Nome completo
            </label>
            <input
              id="name"
              name="name"
              type="text"
              v-model="form.name"
              required
              class="form-input"
              :style="getInputStyle('name')"
              :class="{ 'error': errors.name }"
              placeholder="Digite seu nome completo"
            />
            <div v-if="errors.name" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.name[0] || errors.name }}
            </div>
          </div>

          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">
              <i class="fas fa-envelope"></i>
              E-mail
            </label>
            <input
              id="email"
              name="email"
              type="email"
              v-model="form.email"
              required
              class="form-input"
              :style="getInputStyle('email')"
              :class="{ 'error': errors.email }"
              placeholder="Digite seu e-mail"
            />
            <div v-if="errors.email" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.email[0] || errors.email }}
            </div>
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password" class="form-label">
              <i class="fas fa-lock"></i>
              Senha
            </label>
            <div class="password-input-container">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password"
                required
                class="form-input password-input"
                :style="getInputStyle('password')"
                :class="{ 'error': errors.password }"
                placeholder="Digite sua senha (mínimo 6 caracteres)"
              />
              <button
                type="button"
                @click="togglePasswordVisibility"
                class="password-toggle-btn"
                :style="passwordToggleStyle"
                :title="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <div v-if="errors.password" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.password[0] || errors.password }}
            </div>
          </div>

          <!-- Password Confirmation Field -->
          <div class="form-group">
            <label for="password_confirmation" class="form-label">
              <i class="fas fa-lock"></i>
              Confirmar senha
            </label>
            <div class="password-input-container">
              <input
                id="password_confirmation"
                name="password_confirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'"
                v-model="form.password_confirmation"
                required
                class="form-input password-input"
                :style="getInputStyle('password_confirmation')"
                placeholder="Confirme sua senha"
              />
              <button
                type="button"
                @click="togglePasswordConfirmationVisibility"
                class="password-toggle-btn"
                :style="passwordToggleStyle"
                :title="showPasswordConfirmation ? 'Ocultar senha' : 'Mostrar senha'"
              >
                <i :class="showPasswordConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Company Name Field -->
          <div class="form-group">
            <label for="company_name" class="form-label">
              <i class="fas fa-building"></i>
              Nome da empresa
            </label>
            <input
              id="company_name"
              name="company_name"
              type="text"
              v-model="form.company_name"
              required
              class="form-input"
              :style="getInputStyle('company_name')"
              :class="{ 'error': errors.company_name }"
              placeholder="Digite o nome da sua empresa"
            />
            <div v-if="errors.company_name" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.company_name[0] || errors.company_name }}
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="submit-button"
            :style="buttonStyle"
            @mouseover="onButtonHover"
            @mouseleave="onButtonLeave"
          >
            <span v-if="loading" class="loading-spinner">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            <span class="button-text">
              {{ loading ? 'Criando conta...' : 'Criar conta' }}
            </span>
          </button>
        </form>

        <!-- Footer -->
        <div class="register-footer" :style="footerStyle">
          <p class="footer-text">
            Ao criar uma conta, você concorda com nossos 
            <a href="#" class="footer-link">Termos de Uso</a> e 
            <a href="#" class="footer-link">Política de Privacidade</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        company_name: ''
      },
      errors: {},
      showPassword: false,
      showPasswordConfirmation: false
    }
  },
  computed: {
    ...mapGetters('auth', ['loading']),
    pageStyle() {
      return {
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px',
        position: 'relative',
        overflow: 'hidden'
      }
    },
    containerStyle() {
      return {
        width: '100%',
        maxWidth: '480px',
        position: 'relative',
        zIndex: '10'
      }
    },
    cardStyle() {
      return {
        background: 'rgba(255, 255, 255, 0.95)',
        backdropFilter: 'blur(20px)',
        borderRadius: '20px',
        padding: '40px',
        boxShadow: '0 20px 40px rgba(0, 0, 0, 0.1)',
        border: '1px solid rgba(255, 255, 255, 0.2)'
      }
    },
    headerStyle() {
      return {
        textAlign: 'center',
        marginBottom: '32px'
      }
    },
    logoSectionStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: '24px'
      }
    },
    logoStyle() {
      return {
        marginRight: '12px'
      }
    },
    buttonStyle() {
      return {
        width: '100%',
        padding: '16px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        border: 'none',
        borderRadius: '12px',
        color: 'white',
        fontSize: '16px',
        fontWeight: '600',
        cursor: this.loading ? 'not-allowed' : 'pointer',
        opacity: this.loading ? '0.7' : '1',
        transition: 'all 0.3s ease',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        gap: '8px',
        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
      }
    },
    footerStyle() {
      return {
        marginTop: '24px',
        textAlign: 'center'
      }
    },
    circle1Style() {
      return {
        position: 'absolute',
        top: '10%',
        left: '10%',
        width: '100px',
        height: '100px',
        background: 'rgba(255, 255, 255, 0.1)',
        borderRadius: '50%',
        animation: 'float 6s ease-in-out infinite'
      }
    },
    circle2Style() {
      return {
        position: 'absolute',
        top: '60%',
        right: '15%',
        width: '80px',
        height: '80px',
        background: 'rgba(255, 255, 255, 0.1)',
        borderRadius: '50%',
        animation: 'float 8s ease-in-out infinite reverse'
      }
    },
    circle3Style() {
      return {
        position: 'absolute',
        bottom: '20%',
        left: '20%',
        width: '60px',
        height: '60px',
        background: 'rgba(255, 255, 255, 0.1)',
        borderRadius: '50%',
        animation: 'float 7s ease-in-out infinite'
      }
    },
    passwordToggleStyle() {
      return {
        position: 'absolute',
        right: '12px',
        top: '50%',
        transform: 'translateY(-50%)',
        background: 'transparent',
        border: 'none',
        color: '#6b7280',
        cursor: 'pointer',
        padding: '4px',
        borderRadius: '4px',
        transition: 'all 0.2s ease',
        fontSize: '16px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: '10'
      }
    },
  },
  methods: {
    ...mapActions('auth', ['register']),
    
    getInputStyle(field) {
      const baseStyle = {
        width: '100%',
        padding: '14px 16px',
        fontSize: '16px',
        border: '2px solid #e5e7eb',
        borderRadius: '12px',
        transition: 'all 0.3s ease',
        backgroundColor: 'white',
        outline: 'none'
      }
      
      if (this.errors[field]) {
        baseStyle.borderColor = '#ef4444'
        baseStyle.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)'
      } else {
        baseStyle.borderColor = '#e5e7eb'
      }
      
      return baseStyle
    },
    
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
    
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword
    },
    
    togglePasswordConfirmationVisibility() {
      this.showPasswordConfirmation = !this.showPasswordConfirmation
    },
    
    async handleRegister() {
      this.errors = {}
      
      try {
        await this.register(this.form)
        
        this.$swal.fire({
          title: 'Conta criada com sucesso!',
          text: 'Você já está logado no sistema.',
          icon: 'success',
          timer: 2500,
          showConfirmButton: false
        })
        
        this.$router.push('/')
      } catch (error) {
        console.error('Erro no registro:', error)
        
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          this.$swal.fire({
            title: 'Erro no cadastro',
            text: error.response?.data?.message || 'Verifique os dados e tente novamente.',
            icon: 'error'
          })
        }
      }
    }
  }
}
</script>

<style scoped>
.register-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.form-label i {
  color: #667eea;
}

.form-input {
  font-family: inherit;
}

.form-input:focus {
  border-color: #667eea !important;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #ef4444;
  font-size: 13px;
  font-weight: 500;
}

.brand-title {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.brand-subtitle {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

.register-title {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.register-subtitle {
  color: #6b7280;
  font-size: 16px;
  margin: 0;
}

.login-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.login-link:hover {
  color: #764ba2;
}

.loading-spinner {
  display: flex;
  align-items: center;
}

.footer-text {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
  line-height: 1.5;
}

.footer-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.footer-link:hover {
  color: #764ba2;
}

/* Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input {
  padding-right: 50px !important;
}

.password-toggle-btn:hover {
  color: #667eea !important;
  background: rgba(102, 126, 234, 0.1) !important;
}

.password-toggle-btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .register-page {
    padding: 16px;
  }
  
  .card-style {
    padding: 24px !important;
    border-radius: 16px !important;
  }
  
  .register-title {
    font-size: 24px;
  }
  
  .brand-title {
    font-size: 20px;
  }
  
  .password-toggle-btn {
    right: 8px !important;
    font-size: 14px !important;
  }
  
  .password-input {
    padding-right: 40px !important;
  }
}

@media (max-width: 480px) {
  .register-page {
    padding: 12px;
  }
  
  .register-title {
    font-size: 22px;
  }
}
</style>
