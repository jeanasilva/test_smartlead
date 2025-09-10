const state = {
  sidebarOpen: false,
  loading: false,
  notifications: [],
  theme: 'light'
}

const mutations = {
  TOGGLE_SIDEBAR(state) {
    state.sidebarOpen = !state.sidebarOpen
  },
  
  SET_SIDEBAR(state, open) {
    state.sidebarOpen = open
  },
  
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  
  ADD_NOTIFICATION(state, notification) {
    const id = Date.now()
    state.notifications.push({
      id,
      ...notification,
      createdAt: new Date()
    })
  },
  
  REMOVE_NOTIFICATION(state, id) {
    state.notifications = state.notifications.filter(n => n.id !== id)
  },
  
  CLEAR_NOTIFICATIONS(state) {
    state.notifications = []
  },
  
  SET_THEME(state, theme) {
    state.theme = theme
    localStorage.setItem('theme', theme)
  }
}

const actions = {
  toggleSidebar({ commit }) {
    commit('TOGGLE_SIDEBAR')
  },
  
  setSidebar({ commit }, open) {
    commit('SET_SIDEBAR', open)
  },
  
  setLoading({ commit }, loading) {
    commit('SET_LOADING', loading)
  },
  
  addNotification({ commit }, notification) {
    commit('ADD_NOTIFICATION', notification)
    
    // Auto remove após 5 segundos se não for persistente
    if (!notification.persistent) {
      setTimeout(() => {
        commit('REMOVE_NOTIFICATION', notification.id)
      }, 5000)
    }
  },
  
  removeNotification({ commit }, id) {
    commit('REMOVE_NOTIFICATION', id)
  },
  
  clearNotifications({ commit }) {
    commit('CLEAR_NOTIFICATIONS')
  },
  
  setTheme({ commit }, theme) {
    commit('SET_THEME', theme)
  },
  
  // Helpers para diferentes tipos de notificação
  showSuccess({ dispatch }, message) {
    dispatch('addNotification', {
      type: 'success',
      message,
      title: 'Sucesso!'
    })
  },
  
  showError({ dispatch }, message) {
    dispatch('addNotification', {
      type: 'error',
      message,
      title: 'Erro!'
    })
  },
  
  showWarning({ dispatch }, message) {
    dispatch('addNotification', {
      type: 'warning',
      message,
      title: 'Atenção!'
    })
  },
  
  showInfo({ dispatch }, message) {
    dispatch('addNotification', {
      type: 'info',
      message,
      title: 'Informação'
    })
  }
}

const getters = {
  sidebarOpen: state => state.sidebarOpen,
  loading: state => state.loading,
  notifications: state => state.notifications,
  theme: state => state.theme,
  
  // Notificações por tipo
  successNotifications: state => state.notifications.filter(n => n.type === 'success'),
  errorNotifications: state => state.notifications.filter(n => n.type === 'error'),
  warningNotifications: state => state.notifications.filter(n => n.type === 'warning'),
  infoNotifications: state => state.notifications.filter(n => n.type === 'info')
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
