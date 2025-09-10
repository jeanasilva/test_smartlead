<template>
  <div id="app">
    <!-- Authenticated App Layout -->
    <div v-if="isAuthenticated" class="app-layout">
      <router-view />
    </div>
    
    <!-- Guest Pages (Login/Register) -->
    <div v-else class="guest-layout">
      <router-view />
    </div>

    <!-- Global Loading -->
    <div
      v-if="globalLoading"
      class="loading-overlay"
      :style="loadingOverlayStyle"
    >
      <div class="loading-content" :style="loadingContentStyle">
        <div class="loading-spinner">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
        <span>Carregando...</span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'App',
  computed: {
    ...mapGetters('auth', ['isAuthenticated']),
    globalLoading() {
      return this.$store.getters['auth/loading']
    },
    loadingOverlayStyle() {
      return {
        position: 'fixed',
        top: '0',
        left: '0',
        right: '0',
        bottom: '0',
        background: 'rgba(0, 0, 0, 0.5)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: '9999'
      }
    },
    loadingContentStyle() {
      return {
        background: 'white',
        padding: '24px',
        borderRadius: '12px',
        boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1)',
        display: 'flex',
        alignItems: 'center',
        gap: '16px',
        fontSize: '16px',
        fontWeight: '500',
        color: '#374151'
      }
    }
  },
  async created() {
    // Initialize auth state from localStorage
    if (localStorage.getItem('token')) {
      await this.$store.dispatch('auth/checkAuth')
    }
  }
}
</script>

<style>
/* Global Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
  background-color: #f8fafc;
  color: #1f2937;
  line-height: 1.6;
}

#app {
  min-height: 100vh;
}

.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.guest-layout {
  min-height: 100vh;
}

/* Loading Spinner */
.loading-spinner {
  font-size: 20px;
  color: #667eea;
}

/* Utility Classes */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  body {
    font-size: 14px;
  }
}
</style>
