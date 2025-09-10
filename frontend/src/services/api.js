import axios from 'axios'
import store from '@/store'
import router from '@/router'

const api = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL || 'http://localhost:8000/api',
  timeout: 30000,
})

// Interceptor para adicionar token de autenticação
api.interceptors.request.use(
  (config) => {
    const token = store.getters['auth/token']
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Interceptor para lidar com respostas
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      // Token expirado ou inválido
      store.dispatch('auth/logout')
      
      // Only navigate if not already on login page
      if (router.currentRoute.path !== '/login') {
        router.replace('/login').catch(err => {
          // Ignore NavigationDuplicated errors
          if (err.name !== 'NavigationDuplicated') {
            console.error('Navigation error:', err)
          }
        })
      }
    }
    return Promise.reject(error)
  }
)

// Auth Service
export const authService = {
  async login(credentials) {
    const response = await api.post('/auth/login', credentials)
    return response
  },

  async register(userData) {
    const response = await api.post('/auth/register', userData)
    return response
  },

  async logout() {
    const response = await api.post('/auth/logout')
    return response
  },

  async me() {
    const response = await api.get('/auth/me')
    return response
  },

  async refresh() {
    const response = await api.post('/auth/refresh')
    return response
  }
}

// Task Service
export const taskService = {
  async list(params = {}) {
    const response = await api.get('/tasks', { params })
    return response
  },

  async show(id) {
    const response = await api.get(`/tasks/${id}`)
    return response
  },

  async create(data) {
    const response = await api.post('/tasks', data)
    return response
  },

  async update(id, data) {
    const response = await api.put(`/tasks/${id}`, data)
    return response
  },

  async delete(id) {
    const response = await api.delete(`/tasks/${id}`)
    return response
  }
}

// Company Service
export const companyService = {
  async list(params = {}) {
    const response = await api.get('/companies', { params })
    return response
  },

  async show(id) {
    const response = await api.get(`/companies/${id}`)
    return response
  },

  async create(data) {
    const response = await api.post('/companies', data)
    return response
  },

  async update(id, data) {
    const response = await api.put(`/companies/${id}`, data)
    return response
  },

  async delete(id) {
    const response = await api.delete(`/companies/${id}`)
    return response
  }
}

// User Service
export const userService = {
  async list(params = {}) {
    const response = await api.get('/users', { params })
    return response
  },

  async show(id) {
    const response = await api.get(`/users/${id}`)
    return response
  },

  async create(data) {
    const response = await api.post('/users', data)
    return response
  },

  async update(id, data) {
    const response = await api.put(`/users/${id}`, data)
    return response
  },

  async delete(id) {
    const response = await api.delete(`/users/${id}`)
    return response
  }
}

// Dashboard Service
export const dashboardService = {
  async stats() {
    const response = await api.get('/dashboard/stats')
    return response
  },

  async recentTasks() {
    const response = await api.get('/dashboard/recent-tasks')
    return response
  },

  async myTasks() {
    const response = await api.get('/dashboard/my-tasks')
    return response
  }
}

export default api
