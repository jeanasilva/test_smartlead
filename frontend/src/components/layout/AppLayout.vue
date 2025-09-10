<template>
  <div class="app-layout" :style="layoutStyle">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Content -->
    <main class="main-content" :style="mainContentStyle">
      <div class="content-wrapper" :style="contentWrapperStyle">
        <!-- Page Header -->
        <div class="page-header" v-if="showPageHeader" :style="pageHeaderStyle">
          <div class="page-title-section">
            <h1 class="page-title">{{ pageTitle }}</h1>
            <p class="page-description" v-if="pageDescription">{{ pageDescription }}</p>
          </div>
          <div class="page-actions" v-if="$slots.pageActions">
            <slot name="pageActions"></slot>
          </div>
        </div>
        
        <!-- Breadcrumbs -->
        <nav class="breadcrumbs" v-if="showBreadcrumbs" :style="breadcrumbsStyle">
          <ol class="breadcrumb-list">
            <li class="breadcrumb-item" v-for="(crumb, index) in breadcrumbs" :key="index">
              <router-link 
                v-if="crumb.path && index < breadcrumbs.length - 1" 
                :to="crumb.path"
                class="breadcrumb-link"
              >
                {{ crumb.label }}
              </router-link>
              <span v-else class="breadcrumb-current">{{ crumb.label }}</span>
              <i 
                v-if="index < breadcrumbs.length - 1" 
                class="fas fa-chevron-right breadcrumb-separator"
              ></i>
            </li>
          </ol>
        </nav>
        
        <!-- Content Area -->
        <div class="content-area" :style="contentAreaStyle">
          <slot></slot>
        </div>
      </div>
      
      <!-- Footer -->
      <AppFooter v-if="showFooter" />
    </main>
  </div>
</template>

<script>
import AppHeader from './AppHeader.vue'
import AppFooter from './AppFooter.vue'
import { mapGetters } from 'vuex'

export default {
  name: 'AppLayout',
  components: {
    AppHeader,
    AppFooter
  },
  props: {
    pageTitle: {
      type: String,
      default: ''
    },
    pageDescription: {
      type: String,
      default: ''
    },
    showPageHeader: {
      type: Boolean,
      default: true
    },
    showBreadcrumbs: {
      type: Boolean,
      default: true
    },
    showFooter: {
      type: Boolean,
      default: true
    },
    breadcrumbs: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      isMobile: false
    }
  },
  computed: {
    ...mapGetters('auth', ['isAuthenticated']),
    layoutStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        minHeight: '100vh',
        backgroundColor: '#f8fafc'
      }
    },
    mainContentStyle() {
      return {
        marginLeft: '0',
        marginTop: this.isAuthenticated ? '70px' : '0',
        minHeight: 'calc(100vh - 70px)',
        display: 'flex',
        flexDirection: 'column',
        transition: 'margin-left 0.3s ease'
      }
    },
    contentWrapperStyle() {
      return {
        flex: '1',
        padding: this.isMobile ? '16px' : '24px',
        maxWidth: '100%',
        overflow: 'hidden'
      }
    },
    pageHeaderStyle() {
      return {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: this.isMobile ? 'flex-start' : 'center',
        marginBottom: '24px',
        flexDirection: this.isMobile ? 'column' : 'row',
        gap: this.isMobile ? '16px' : '0'
      }
    },
    breadcrumbsStyle() {
      return {
        marginBottom: '20px',
        padding: '12px 0'
      }
    },
    contentAreaStyle() {
      return {
        flex: '1',
        position: 'relative'
      }
    },
    overlayStyle() {
      return {
        position: 'fixed',
        top: '0',
        left: '0',
        right: '0',
        bottom: '0',
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        zIndex: '998'
      }
    }
  },
  methods: {
    handleResize() {
      this.isMobile = window.innerWidth < 768
    },
    generateBreadcrumbs() {
      const route = this.$route
      const matched = route.matched.filter(item => item.meta && item.meta.breadcrumb)
      
      const breadcrumbs = matched.map(match => {
        return {
          label: match.meta.breadcrumb,
          path: match.path
        }
      })
      
      return breadcrumbs
    }
  },
  created() {
    this.handleResize()
  },
  mounted() {
    window.addEventListener('resize', this.handleResize)
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (this.isMobile && this.showSidebar && !e.target.closest('.app-sidebar') && !e.target.closest('.mobile-menu-btn')) {
        this.closeMobileMenu()
      }
    })
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.handleResize)
  },
  watch: {
    isAuthenticated(newVal) {
      if (newVal) {
        // Sidebar removido
      }
    }
  }
}
</script>

<style scoped>
.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 8px 0;
  line-height: 1.2;
}

.page-description {
  font-size: 16px;
  color: #6b7280;
  margin: 0;
  line-height: 1.5;
}

.breadcrumb-list {
  display: flex;
  align-items: center;
  list-style: none;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
  gap: 8px;
}

.breadcrumb-item {
  display: flex;
  align-items: center;
  font-size: 14px;
}

.breadcrumb-link {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.3s ease;
}

.breadcrumb-link:hover {
  color: #667eea;
}

.breadcrumb-current {
  color: #1f2937;
  font-weight: 500;
}

.breadcrumb-separator {
  margin: 0 8px;
  color: #d1d5db;
  font-size: 12px;
}

.page-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Mobile Styles */
@media (max-width: 768px) {
  .page-title {
    font-size: 24px;
  }
  
  .page-description {
    font-size: 14px;
  }
  
  .breadcrumb-list {
    font-size: 13px;
  }
}

/* Smooth transitions */
* {
  transition: margin-left 0.3s ease;
}
</style>
