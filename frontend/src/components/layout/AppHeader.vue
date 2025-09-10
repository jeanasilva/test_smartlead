<template>
  <header class="app-header" :style="headerStyle">
    <div class="header-container">
      <!-- Logo e Brand -->
      <div class="brand-section" @click="$router.push('/')" :style="brandStyle">
        <div class="logo-wrapper" :style="logoStyle">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="8" fill="url(#gradient)" />
            <path d="M8 16L14 22L24 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <defs>
              <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
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

      <!-- Navigation Menu (Desktop) -->
      <nav class="nav-menu" :style="navStyle" v-if="isAuthenticated">
        <router-link 
          v-for="item in menuItems" 
          :key="item.path"
          :to="item.path"
          class="nav-item"
          :style="navItemStyle"
          @mouseover="onNavHover"
          @mouseleave="onNavLeave"
        >
          <i :class="item.icon" class="nav-icon"></i>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- User Menu -->
      <div class="user-menu" v-if="isAuthenticated">
        <div class="user-info" @click="toggleUserDropdown" :style="userInfoStyle">
          <div class="user-avatar" :style="avatarStyle">
            {{ userInitials }}
          </div>
          <div class="user-details">
            <span class="user-name">{{ userName }}</span>
            <span class="user-company">{{ userCompany?.name || 'Empresa' }}</span>
          </div>
          <i class="fas fa-chevron-down dropdown-icon" :class="{ 'rotated': showUserDropdown }"></i>
        </div>

        <!-- Dropdown Menu -->
        <transition name="dropdown">
          <div v-if="showUserDropdown" class="user-dropdown" :style="dropdownStyle">
            <router-link to="/profile" class="dropdown-item" :style="dropdownItemStyle">
              <i class="fas fa-user"></i>
              <span>Meu Perfil</span>
            </router-link>
            <router-link to="/settings" class="dropdown-item" :style="dropdownItemStyle">
              <i class="fas fa-cog"></i>
              <span>Configurações</span>
            </router-link>
            <div class="dropdown-divider"></div>
            <button @click="handleLogout" class="dropdown-item logout-btn" :style="dropdownItemStyle">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sair</span>
            </button>
          </div>
        </transition>
      </div>

      </div>
    </header>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'AppHeader',
  data() {
    return {
      showUserDropdown: false
    }
  },
  computed: {
    ...mapGetters('auth', ['isAuthenticated', 'user', 'isAdmin']),
    menuItems() {
      const items = [
        { path: '/', label: 'Dashboard', icon: 'fas fa-tachometer-alt' },
        { path: '/tasks', label: 'Tarefas', icon: 'fas fa-tasks' },
        { path: '/companies', label: 'Empresas', icon: 'fas fa-building' },
        { path: '/reports', label: 'Relatórios', icon: 'fas fa-chart-bar' }
      ]
      
      // Adicionar Management apenas para admins
      if (this.isAdmin) {
        items.push({ path: '/management', label: 'Usuários', icon: 'fas fa-users-cog' })
      }
      
      return items
    },
    userName() {
      return this.user?.name || 'Usuário'
    },
    userInitials() {
      if (!this.user?.name) return 'U'
      return this.user.name.split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    },
    userCompany() {
      return this.user?.company || null
    },
    headerStyle() {
      return {
        position: 'fixed',
        top: '0',
        left: '0',
        right: '0',
        height: '70px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        backdropFilter: 'blur(10px)',
        borderBottom: '1px solid rgba(255, 255, 255, 0.1)',
        zIndex: '1000',
        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
      }
    },
    brandStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        cursor: 'pointer',
        transition: 'all 0.3s ease'
      }
    },
    logoStyle() {
      return {
        marginRight: '12px',
        transition: 'transform 0.3s ease'
      }
    },
    navStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px'
      }
    },
    navItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '8px 16px',
        color: 'rgba(255, 255, 255, 0.9)',
        textDecoration: 'none',
        borderRadius: '8px',
        transition: 'all 0.3s ease',
        fontSize: '14px',
        fontWeight: '500'
      }
    },
    userInfoStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
        padding: '8px',
        borderRadius: '12px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        color: 'white'
      }
    },
    avatarStyle() {
      return {
        width: '40px',
        height: '40px',
        borderRadius: '50%',
        background: 'linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '14px',
        fontWeight: '600',
        border: '2px solid rgba(255, 255, 255, 0.2)'
      }
    },
    dropdownStyle() {
      return {
        position: 'absolute',
        top: '60px',
        right: '0',
        minWidth: '200px',
        background: 'white',
        borderRadius: '12px',
        boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        border: '1px solid rgba(0, 0, 0, 0.05)',
        overflow: 'hidden',
        zIndex: '50'
      }
    },
    dropdownItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
        padding: '12px 16px',
        color: '#374151',
        textDecoration: 'none',
        transition: 'all 0.2s ease',
        fontSize: '14px',
        border: 'none',
        background: 'transparent',
        width: '100%',
        cursor: 'pointer'
      }
    }
  },
  methods: {
    ...mapActions('auth', ['logout']),
    toggleUserDropdown() {
      this.showUserDropdown = !this.showUserDropdown
    },
    async handleLogout() {
      try {
        await this.logout()
        
        // Use replace instead of push to avoid navigation duplicates
        // Also check if we're not already on login page
        if (this.$route.path !== '/login') {
          this.$router.replace('/login').catch(err => {
            // Ignore NavigationDuplicated errors
            if (err.name !== 'NavigationDuplicated') {
              console.error('Navigation error:', err)
            }
          })
        }
        
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
    },
    onNavHover(event) {
      event.target.style.background = 'rgba(255, 255, 255, 0.1)'
      event.target.style.transform = 'translateY(-1px)'
    },
    onNavLeave(event) {
      event.target.style.background = 'transparent'
      event.target.style.transform = 'translateY(0)'
    }
  },
  mounted() {
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!this.$el.contains(e.target)) {
        this.showUserDropdown = false
      }
    })
  }
}
</script>

<style scoped>
.app-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
  padding: 0 24px;
  max-width: 100%;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-title {
  font-size: 20px;
  font-weight: 700;
  color: white;
  margin: 0;
  line-height: 1;
}

.brand-subtitle {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 500;
}

.nav-icon {
  font-size: 16px;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
}

.user-company {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

.dropdown-icon {
  font-size: 12px;
  transition: transform 0.3s ease;
}

.dropdown-icon.rotated {
  transform: rotate(180deg);
}

.dropdown-divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.1);
  margin: 4px 0;
}

.logout-btn {
  color: #dc2626 !important;
}

.dropdown-item:hover {
  background-color: #f9fafb !important;
}

.logout-btn:hover {
  background-color: #fef2f2 !important;
}

/* Animations */
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.3s ease;
}

.dropdown-enter, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Mobile Styles */
@media (max-width: 768px) {
  .header-container {
    padding: 0 16px;
  }
  
  .nav-menu {
    display: none !important;
  }
  
  .mobile-menu-btn {
    display: flex !important;
  }
  
  .user-details {
    display: none;
  }
  
  .brand-title {
    font-size: 18px;
  }
}

@media (max-width: 480px) {
  .brand-subtitle {
    display: none;
  }
}
</style>
