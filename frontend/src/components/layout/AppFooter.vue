<template>
  <footer class="app-footer" :style="footerStyle">
    <div class="footer-container" :style="containerStyle">
      <!-- Footer Content -->
      <div class="footer-content">
        <!-- Brand Section -->
        <div class="footer-brand">
          <div class="brand-logo" :style="logoStyle">
            <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="8" fill="url(#footerGradient)" />
              <path d="M8 16L14 22L24 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="footerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#667eea"/>
                  <stop offset="100%" stop-color="#764ba2"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
          <div class="brand-info">
            <span class="brand-name">SmartLead Tasks</span>
            <p class="brand-description">Gerencie suas tarefas de forma inteligente</p>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-links">
          <div class="link-group">
            <h4 class="link-title">Navegação</h4>
            <ul class="link-list">
              <li><router-link to="/" class="footer-link">Dashboard</router-link></li>
              <li><router-link to="/tasks" class="footer-link">Tarefas</router-link></li>
              <li><router-link to="/companies" class="footer-link">Empresas</router-link></li>
              <li><router-link to="/reports" class="footer-link">Relatórios</router-link></li>
            </ul>
          </div>

          <div class="link-group">
            <h4 class="link-title">Conta</h4>
            <ul class="link-list">
              <li><router-link to="/profile" class="footer-link">Meu Perfil</router-link></li>
              <li><router-link to="/settings" class="footer-link">Configurações</router-link></li>
              <li><button @click="handleLogout" class="footer-link logout-link">Sair</button></li>
            </ul>
          </div>

          <div class="link-group">
            <h4 class="link-title">Suporte</h4>
            <ul class="link-list">
              <li><a href="#" class="footer-link">Ajuda</a></li>
              <li><a href="#" class="footer-link">Documentação</a></li>
              <li><a href="#" class="footer-link">Contato</a></li>
            </ul>
          </div>
        </div>

        <!-- Status Info -->
        <div class="footer-status">
          <div class="status-item">
            <div class="status-indicator online" :style="statusStyle"></div>
            <span class="status-text">Sistema Online</span>
          </div>
          <div class="server-info">
            <i class="fas fa-server"></i>
            <span>Servidor: {{ serverStatus }}</span>
          </div>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="footer-bottom" :style="bottomStyle">
        <div class="copyright">
          <span>&copy; {{ currentYear }} SmartLead Tasks. Todos os direitos reservados.</span>
        </div>
        <div class="footer-meta">
          <span class="version">v1.0.0</span>
          <span class="divider">•</span>
          <span class="build">Build {{ buildNumber }}</span>
          <span class="divider">•</span>
          <span class="last-updated">Atualizado em {{ lastUpdated }}</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'AppFooter',
  data() {
    return {
      currentYear: new Date().getFullYear(),
      buildNumber: Math.random().toString(36).substr(2, 8).toUpperCase(),
      serverStatus: 'Ativo',
      lastUpdated: this.formatDate(new Date())
    }
  },
  computed: {
    footerStyle() {
      return {
        background: 'linear-gradient(135deg, #1f2937 0%, #111827 100%)',
        borderTop: '1px solid rgba(255, 255, 255, 0.1)',
        color: 'rgba(255, 255, 255, 0.8)',
        marginTop: 'auto'
      }
    },
    containerStyle() {
      return {
        maxWidth: '1200px',
        margin: '0 auto',
        padding: '40px 24px 20px'
      }
    },
    logoStyle() {
      return {
        marginRight: '12px',
        flexShrink: '0'
      }
    },
    statusStyle() {
      return {
        width: '8px',
        height: '8px',
        borderRadius: '50%',
        backgroundColor: '#10b981',
        marginRight: '8px',
        animation: 'pulse 2s infinite'
      }
    },
    bottomStyle() {
      return {
        paddingTop: '20px',
        marginTop: '30px',
        borderTop: '1px solid rgba(255, 255, 255, 0.1)',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        flexWrap: 'wrap',
        gap: '16px'
      }
    }
  },
  methods: {
    ...mapActions('auth', ['logout']),
    
    formatDate(date) {
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    
    async handleLogout() {
      try {
        await this.logout()
        this.$router.push('/login')
        this.$swal.fire({
          title: 'Logout realizado!',
          text: 'Até logo!',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
      } catch (error) {
        console.error('Erro ao fazer logout:', error)
      }
    }
  }
}
</script>

<style scoped>
.footer-content {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 40px;
  margin-bottom: 0;
}

.footer-brand {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.brand-name {
  font-size: 18px;
  font-weight: 700;
  color: white;
  display: block;
  margin-bottom: 8px;
}

.brand-description {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  line-height: 1.5;
}

.footer-links {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.link-group {
  display: flex;
  flex-direction: column;
}

.link-title {
  font-size: 14px;
  font-weight: 600;
  color: white;
  margin: 0 0 16px 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.link-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.link-list li {
  margin-bottom: 10px;
}

.footer-link {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s ease;
  border: none;
  background: none;
  cursor: pointer;
  padding: 0;
}

.footer-link:hover {
  color: #667eea;
  transform: translateX(4px);
}

.logout-link {
  font-family: inherit;
  font-size: inherit;
}

.footer-status {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.status-item {
  display: flex;
  align-items: center;
}

.status-text {
  font-size: 14px;
  font-weight: 500;
  color: white;
}

.server-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
}

.copyright {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
}

.footer-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
}

.divider {
  color: rgba(255, 255, 255, 0.3);
}

.version {
  font-weight: 600;
  color: #667eea;
}

/* Animations */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Mobile Responsive */
@media (max-width: 1024px) {
  .footer-content {
    grid-template-columns: 1fr 1fr;
    gap: 30px;
  }
  
  .footer-status {
    grid-column: span 2;
  }
}

@media (max-width: 768px) {
  .footer-content {
    grid-template-columns: 1fr;
    gap: 30px;
    text-align: center;
  }
  
  .footer-links {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .footer-brand {
    justify-content: center;
  }
  
  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .footer-links {
    grid-template-columns: 1fr;
  }
  
  .footer-meta {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>
