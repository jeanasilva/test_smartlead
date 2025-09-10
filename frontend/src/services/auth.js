import api from './api'

export default {
  async login(credentials) {
    const response = await api.post('/auth/login', credentials)
    return response.data
  },

  async register(userData) {
    const response = await api.post('/auth/register', userData)
    return response.data
  },

  async logout() {
    try {
      await api.post('/auth/logout')
    } catch (error) {
      // Ignorar erro se o logout falhar no servidor
      console.warn('Logout error:', error)
    }
  },

  async me() {
    const response = await api.get('/auth/me')
    return response.data
  },

  async refresh() {
    const response = await api.post('/auth/refresh')
    return response.data
  }
}
