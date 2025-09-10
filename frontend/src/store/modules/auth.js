import authService from '@/services/auth'

const state = {
  token: localStorage.getItem('token'),
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  isAuthenticated: !!localStorage.getItem('token'),
  loading: false
}

const mutations = {
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  
  SET_TOKEN(state, token) {
    state.token = token
    state.isAuthenticated = !!token
    if (token) {
      localStorage.setItem('token', token)
    } else {
      localStorage.removeItem('token')
    }
  },
  
  SET_USER(state, user) {
    state.user = user
    if (user) {
      localStorage.setItem('user', JSON.stringify(user))
    } else {
      localStorage.removeItem('user')
    }
  },
  
  CLEAR_AUTH(state) {
    state.token = null
    state.user = null
    state.isAuthenticated = false
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }
}

const actions = {
  async login({ commit }, credentials) {
    try {
      commit('SET_LOADING', true)
      const response = await authService.login(credentials)
      
      // O backend retorna 'token', não 'access_token'
      commit('SET_TOKEN', response.token)
      commit('SET_USER', response.user)
      
      return response
    } catch (error) {
      commit('CLEAR_AUTH')
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async register({ commit }, userData) {
    try {
      commit('SET_LOADING', true)
      const response = await authService.register(userData)
      
      // O backend retorna 'token', não 'access_token'
      commit('SET_TOKEN', response.token)
      commit('SET_USER', response.user)
      
      return response
    } catch (error) {
      commit('CLEAR_AUTH')
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async logout({ commit }) {
    try {
      await authService.logout()
    } catch (error) {
      console.warn('Logout error:', error)
    } finally {
      commit('CLEAR_AUTH')
    }
  },

  async checkAuth({ commit, state }) {
    if (!state.token) {
      return false
    }

    try {
      const response = await authService.me()
      commit('SET_USER', response.user)
      return true
    } catch (error) {
      commit('CLEAR_AUTH')
      return false
    }
  },

  async fetchUser({ commit, state }) {
    if (!state.token) {
      return null
    }

    try {
      const response = await authService.me()
      commit('SET_USER', response.user)
      return response.user
    } catch (error) {
      commit('CLEAR_AUTH')
      throw error
    }
  },

  async refreshToken({ commit, state }) {
    if (!state.token) {
      return null
    }

    try {
      const response = await authService.refresh()
      commit('SET_TOKEN', response.access_token)
      return response
    } catch (error) {
      commit('CLEAR_AUTH')
      throw error
    }
  }
}

const getters = {
  token: state => state.token,
  user: state => state.user,
  isAuthenticated: state => state.isAuthenticated,
  loading: state => state.loading,
  userName: state => state.user?.name || '',
  userEmail: state => state.user?.email || '',
  userCompany: state => state.user?.company || null,
  userRole: state => state.user?.role || 'user',
  isAdmin: state => state.user?.role === 'admin',
  isUser: state => state.user?.role === 'user'
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
