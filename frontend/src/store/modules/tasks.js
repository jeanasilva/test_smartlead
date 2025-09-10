import tasksService from '@/services/tasks'

const state = {
  tasks: [],
  currentTask: null,
  stats: {},
  recentTasks: [],
  myTasks: {},
  loading: false,
  filters: {
    status: '',
    priority: '',
    search: ''
  }
}

const mutations = {
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  
  SET_TASKS(state, tasks) {
    state.tasks = tasks
  },
  
  SET_CURRENT_TASK(state, task) {
    state.currentTask = task
  },
  
  ADD_TASK(state, task) {
    state.tasks.unshift(task)
  },
  
  UPDATE_TASK(state, updatedTask) {
    const index = state.tasks.findIndex(task => task.id === updatedTask.id)
    if (index !== -1) {
      state.tasks.splice(index, 1, updatedTask)
    }
  },
  
  REMOVE_TASK(state, taskId) {
    state.tasks = state.tasks.filter(task => task.id !== taskId)
  },
  
  SET_STATS(state, stats) {
    state.stats = stats
  },
  
  SET_RECENT_TASKS(state, tasks) {
    state.recentTasks = tasks
  },
  
  SET_MY_TASKS(state, myTasks) {
    state.myTasks = myTasks
  },
  
  SET_FILTERS(state, filters) {
    state.filters = { ...state.filters, ...filters }
  },
  
  CLEAR_FILTERS(state) {
    state.filters = {
      status: '',
      priority: '',
      search: ''
    }
  }
}

const actions = {
  async fetchTasks({ commit, state }) {
    try {
      commit('SET_LOADING', true)
      const response = await tasksService.getTasks(state.filters)
      commit('SET_TASKS', response.data)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar tarefas:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async fetchTask({ commit }, id) {
    try {
      commit('SET_LOADING', true)
      const response = await tasksService.getTask(id)
      commit('SET_CURRENT_TASK', response.data)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar tarefa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async createTask({ commit }, taskData) {
    try {
      commit('SET_LOADING', true)
      const response = await tasksService.createTask(taskData)
      commit('ADD_TASK', response.data)
      return response
    } catch (error) {
      console.error('Erro ao criar tarefa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async updateTask({ commit }, { id, taskData }) {
    try {
      commit('SET_LOADING', true)
      const response = await tasksService.updateTask(id, taskData)
      commit('UPDATE_TASK', response.data)
      return response
    } catch (error) {
      console.error('Erro ao atualizar tarefa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async deleteTask({ commit }, id) {
    try {
      commit('SET_LOADING', true)
      await tasksService.deleteTask(id)
      commit('REMOVE_TASK', id)
      return true
    } catch (error) {
      console.error('Erro ao excluir tarefa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async fetchStats({ commit }) {
    try {
      const response = await tasksService.getDashboardStats()
      commit('SET_STATS', response)
      return response
    } catch (error) {
      console.error('Erro ao buscar estatísticas:', error)
      throw error
    }
  },

  async fetchRecentTasks({ commit }, limit = 10) {
    try {
      const response = await tasksService.getRecentTasks(limit)
      commit('SET_RECENT_TASKS', response.data)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar tarefas recentes:', error)
      throw error
    }
  },

  async fetchMyTasks({ commit }) {
    try {
      const response = await tasksService.getMyTasks()
      commit('SET_MY_TASKS', response)
      return response
    } catch (error) {
      console.error('Erro ao buscar minhas tarefas:', error)
      throw error
    }
  },

  setFilters({ commit }, filters) {
    commit('SET_FILTERS', filters)
  },

  clearFilters({ commit }) {
    commit('CLEAR_FILTERS')
  },

  async exportTasks({ state }, additionalFilters = {}) {
    try {
      const filters = { ...state.filters, ...additionalFilters }
      const response = await tasksService.exportTasksCSV(filters)
      
      // Download do arquivo
      const blob = new Blob([response.data], { type: 'text/csv' })
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', `tarefas_${new Date().getTime()}.csv`)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      return true
    } catch (error) {
      console.error('Erro ao exportar tarefas:', error)
      throw error
    }
  }
}

const getters = {
  tasks: state => state.tasks,
  currentTask: state => state.currentTask,
  stats: state => state.stats,
  recentTasks: state => state.recentTasks,
  myTasks: state => state.myTasks,
  loading: state => state.loading,
  filters: state => state.filters,
  
  filteredTasks: state => {
    let filtered = state.tasks

    if (state.filters.status) {
      filtered = filtered.filter(task => task.status === state.filters.status)
    }

    if (state.filters.priority) {
      filtered = filtered.filter(task => task.priority === state.filters.priority)
    }

    if (state.filters.search) {
      const search = state.filters.search.toLowerCase()
      filtered = filtered.filter(task => 
        task.title.toLowerCase().includes(search) ||
        task.description?.toLowerCase().includes(search)
      )
    }

    return filtered
  },
  
  tasksByStatus: state => {
    const tasks = state.tasks
    return {
      pendente: tasks.filter(task => task.status === 'pendente'),
      em_andamento: tasks.filter(task => task.status === 'em_andamento'),
      concluida: tasks.filter(task => task.status === 'concluida')
    }
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
