import api from './api'

export default {
  async getTasks(filters = {}) {
    const response = await api.get('/tasks', { params: filters })
    return response.data
  },

  async getTask(id) {
    const response = await api.get(`/tasks/${id}`)
    return response.data
  },

  async createTask(taskData) {
    const response = await api.post('/tasks', taskData)
    return response.data
  },

  async updateTask(id, taskData) {
    const response = await api.put(`/tasks/${id}`, taskData)
    return response.data
  },

  async deleteTask(id) {
    const response = await api.delete(`/tasks/${id}`)
    return response.data
  },

  async getDashboardStats() {
    const response = await api.get('/dashboard/stats')
    return response.data
  },

  async getRecentTasks(limit = 10) {
    const response = await api.get('/dashboard/recent-tasks', { params: { limit } })
    return response.data
  },

  async getMyTasks() {
    const response = await api.get('/dashboard/my-tasks')
    return response.data
  },

  async exportTasksCSV(filters = {}) {
    const response = await api.get('/export/tasks/csv', {
      params: filters,
      responseType: 'blob'
    })
    return response
  },

  async getReportSummary() {
    const response = await api.get('/export/report/summary')
    return response.data
  }
}
