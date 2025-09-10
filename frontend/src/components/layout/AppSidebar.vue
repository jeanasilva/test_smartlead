<template>
  <transition name="sidebar">
    <aside 
      v-if="isVisible" 
      class="app-sidebar"
      :style="sidebarStyle"
      :class="{ 'mobile': isMobile }"
    >
      <!-- Sidebar Header -->
      <div class="sidebar-header" :style="headerStyle">
        <div class="sidebar-brand" @click="$router.push('/')">
          <div class="brand-logo" :style="logoStyle">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="8" fill="url(#sidebarGradient)" />
              <path d="M8 16L14 22L24 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="sidebarGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#667eea"/>
                  <stop offset="100%" stop-color="#764ba2"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
          <div class="brand-info" v-if="!collapsed">
            <h2 class="brand-title">SmartLead</h2>
            <span class="brand-subtitle">Task Management</span>
          </div>
        </div>
        
        <button 
          @click="toggleCollapse" 
          class="collapse-btn"
          :style="collapseBtnStyle"
          v-if="!isMobile"
          :title="collapsed ? 'Expandir menu' : 'Recolher menu'"
        >
          <i :class="collapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
        </button>
      </div>

      <!-- Navigation Menu -->
      <nav class="sidebar-nav" :style="navStyle">
        <div class="nav-section">
          <h3 class="nav-section-title" v-if="!collapsed">Principal</h3>
          
          <router-link 
            v-for="item in mainMenuItems" 
            :key="item.path"
            :to="item.path"
            class="nav-item"
            :style="navItemStyle"
            @mouseover="onItemHover"
            @mouseleave="onItemLeave"
            :title="collapsed ? item.label : ''"
          >
            <div class="nav-item-icon" :style="iconStyle">
              <i :class="item.icon"></i>
            </div>
            <span class="nav-item-text" v-if="!collapsed">{{ item.label }}</span>
            <div class="nav-item-badge" v-if="item.badge && !collapsed" :style="badgeStyle">
              {{ item.badge }}
            </div>
          </router-link>
        </div>

        <div class="nav-section" v-if="!collapsed">
          <h3 class="nav-section-title">Gestão</h3>
          
          <router-link 
            v-for="item in managementMenuItems" 
            :key="item.path"
            :to="item.path"
            class="nav-item"
            :style="navItemStyle"
            @mouseover="onItemHover"
            @mouseleave="onItemLeave"
            :title="collapsed ? item.label : ''"
          >
            <div class="nav-item-icon" :style="iconStyle">
              <i :class="item.icon"></i>
            </div>
            <span class="nav-item-text">{{ item.label }}</span>
          </router-link>
        </div>

        <!-- Menu de gestão quando fechado -->
        <div class="nav-section" v-if="collapsed">
          <router-link 
            v-for="item in managementMenuItems" 
            :key="item.path"
            :to="item.path"
            class="nav-item collapsed-item"
            :style="navItemStyle"
            @mouseover="onItemHover"
            @mouseleave="onItemLeave"
            :title="item.label"
          >
            <div class="nav-item-icon" :style="iconStyle">
              <i :class="item.icon"></i>
            </div>
          </router-link>
        </div>
      </nav>

      <!-- User Info -->
      <div class="sidebar-footer" :style="footerStyle" v-if="user">
        <div class="user-card" :style="userCardStyle">
          <div class="user-avatar" :style="avatarStyle">
            {{ userInitials }}
          </div>
          <div class="user-info" v-if="!collapsed">
            <span class="user-name">{{ user.name }}</span>
            <span class="user-status">Online</span>
          </div>
        </div>
      </div>

      <!-- Mobile Overlay -->
      <div 
        v-if="isMobile && isVisible" 
        @click="closeSidebar"
        class="sidebar-overlay"
        :style="overlayStyle"
      ></div>
    </aside>
  </transition>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'AppSidebar',
  props: {
    isVisible: {
      type: Boolean,
      default: true
    },
    isMobile: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      collapsed: true, // Inicia fechado
    }
  },
  computed: {
    ...mapGetters('auth', ['user', 'isAdmin']),
    mainMenuItems() {
      const items = [
        { 
          path: '/', 
          label: 'Dashboard', 
          icon: 'fas fa-tachometer-alt',
          badge: null
        },
        { 
          path: '/tasks', 
          label: 'Tarefas', 
          icon: 'fas fa-tasks',
          badge: '12'
        }
      ];

      // Admin pode ver todas as empresas
      if (this.isAdmin) {
        items.push({ 
          path: '/companies', 
          label: 'Empresas', 
          icon: 'fas fa-building',
          badge: null
        });
      }

      return items;
    },
    managementMenuItems() {
      return [
        { 
          path: '/reports', 
          label: 'Relatórios', 
          icon: 'fas fa-chart-bar'
        },
        { 
          path: '/profile', 
          label: 'Perfil', 
          icon: 'fas fa-user-circle'
        }
      ];
    },
    userInitials() {
      if (!this.user?.name) return 'U'
      return this.user.name.split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    },
    sidebarStyle() {
      return {
        position: 'fixed',
        left: '0',
        top: this.isMobile ? '0' : '70px',
        bottom: '0',
        width: this.collapsed ? '80px' : '280px',
        background: 'linear-gradient(180deg, #1f2937 0%, #111827 100%)',
        borderRight: '1px solid rgba(255, 255, 255, 0.1)',
        zIndex: this.isMobile ? '1001' : '999',
        transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
        display: 'flex',
        flexDirection: 'column',
        boxShadow: this.collapsed ? '2px 0 8px rgba(0, 0, 0, 0.15)' : '4px 0 12px rgba(0, 0, 0, 0.15)',
        backdropFilter: 'blur(10px)'
      }
    },
    headerStyle() {
      return {
        padding: '20px',
        borderBottom: '1px solid rgba(255, 255, 255, 0.1)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        minHeight: '80px'
      }
    },
    logoStyle() {
      return {
        marginRight: this.collapsed ? '0' : '12px',
        transition: 'margin 0.3s ease'
      }
    },
    collapseBtnStyle() {
      return {
        width: '36px',
        height: '36px',
        border: 'none',
        background: 'rgba(255, 255, 255, 0.1)',
        color: 'rgba(255, 255, 255, 0.8)',
        borderRadius: '10px',
        cursor: 'pointer',
        transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '14px',
        position: 'absolute',
        right: '16px',
        top: '50%',
        transform: 'translateY(-50%)',
        backdropFilter: 'blur(10px)'
      }
    },
    navStyle() {
      return {
        flex: '1',
        padding: '20px',
        paddingTop: '10px',
        overflowY: 'auto'
      }
    },
    navItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        justifyContent: this.collapsed ? 'center' : 'flex-start',
        padding: this.collapsed ? '12px 16px' : '12px 16px',
        margin: '4px 8px',
        color: 'rgba(255, 255, 255, 0.8)',
        textDecoration: 'none',
        borderRadius: '12px',
        transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
        position: 'relative',
        fontSize: '14px',
        fontWeight: '500',
        minHeight: '48px',
        cursor: 'pointer'
      }
    },
    iconStyle() {
      return {
        width: '20px',
        height: '20px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: this.collapsed ? '0' : '12px',
        fontSize: this.collapsed ? '18px' : '16px',
        transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
        flexShrink: '0'
      }
    },
    badgeStyle() {
      return {
        marginLeft: 'auto',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        fontSize: '11px',
        fontWeight: '600',
        padding: '2px 6px',
        borderRadius: '10px',
        minWidth: '18px',
        textAlign: 'center'
      }
    },
    footerStyle() {
      return {
        padding: '20px',
        borderTop: '1px solid rgba(255, 255, 255, 0.1)'
      }
    },
    userCardStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: this.collapsed ? '0' : '12px',
        justifyContent: this.collapsed ? 'center' : 'flex-start'
      }
    },
    avatarStyle() {
      return {
        width: '36px',
        height: '36px',
        borderRadius: '50%',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '13px',
        fontWeight: '600',
        color: 'white',
        flexShrink: '0'
      }
    },
    overlayStyle() {
      return {
        position: 'fixed',
        top: '0',
        left: '0',
        right: '0',
        bottom: '0',
        background: 'rgba(0, 0, 0, 0.5)',
        zIndex: '-1'
      }
    }
  },
  methods: {
    toggleCollapse() {
      this.collapsed = !this.collapsed
      this.$emit('collapse-changed', this.collapsed)
    },
    closeSidebar() {
      this.$emit('close')
    },
    onItemHover(event) {
      event.target.style.background = 'rgba(255, 255, 255, 0.1)'
      event.target.style.transform = 'translateX(4px)'
    },
    onItemLeave(event) {
      if (!event.target.classList.contains('router-link-active')) {
        event.target.style.background = 'transparent'
      }
      event.target.style.transform = 'translateX(0)'
    }
  },
  watch: {
    $route() {
      if (this.isMobile) {
        this.$emit('close')
      }
    }
  }
}
</script>

<style scoped>
.app-sidebar {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

.app-sidebar::-webkit-scrollbar {
  width: 6px;
}

.app-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.app-sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.sidebar-brand:hover {
  opacity: 0.8;
}

.brand-title {
  font-size: 18px;
  font-weight: 700;
  color: white;
  margin: 0;
  line-height: 1;
}

.brand-subtitle {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
}

.collapse-btn:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  color: white !important;
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
}

.nav-section {
  margin-bottom: 24px;
}

.nav-section-title {
  font-size: 11px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.5);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0 0 12px 0;
  padding: 0 16px;
}

.nav-item-text {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-item.router-link-active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.3) 0%, rgba(118, 75, 162, 0.3) 100%) !important;
  color: white !important;
  border-left: 3px solid #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  color: white !important;
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
}

.collapsed-item {
  justify-content: center !important;
  padding: 12px !important;
}

.collapsed-item:hover {
  transform: scale(1.05) !important;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: white;
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-status {
  font-size: 12px;
  color: #10b981;
  font-weight: 500;
}

/* Mobile Styles */
.app-sidebar.mobile {
  top: 0 !important;
  height: 100vh;
  width: 280px !important;
}

/* Animations */
.sidebar-enter-active, .sidebar-leave-active {
  transition: all 0.3s ease;
}

.sidebar-enter, .sidebar-leave-to {
  transform: translateX(-100%);
}

@media (max-width: 768px) {
  .app-sidebar {
    z-index: 1001 !important;
  }
}
</style>
