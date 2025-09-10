import companiesService from '@/services/companies'

const state = {
  companies: [],
  currentCompany: null,
  loading: false
}

const mutations = {
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  
  SET_COMPANIES(state, companies) {
    state.companies = companies
  },
  
  SET_CURRENT_COMPANY(state, company) {
    state.currentCompany = company
  },
  
  ADD_COMPANY(state, company) {
    state.companies.unshift(company)
  },
  
  UPDATE_COMPANY(state, updatedCompany) {
    const index = state.companies.findIndex(company => company.id === updatedCompany.id)
    if (index !== -1) {
      state.companies.splice(index, 1, updatedCompany)
    }
  },
  
  REMOVE_COMPANY(state, companyId) {
    state.companies = state.companies.filter(company => company.id !== companyId)
  }
}

const actions = {
  async fetchCompanies({ commit }) {
    try {
      commit('SET_LOADING', true)
      const response = await companiesService.getCompanies()
      commit('SET_COMPANIES', response.data)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar empresas:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async fetchCompany({ commit }, id) {
    try {
      commit('SET_LOADING', true)
      const response = await companiesService.getCompany(id)
      commit('SET_CURRENT_COMPANY', response.data)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar empresa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async createCompany({ commit }, companyData) {
    try {
      commit('SET_LOADING', true)
      const response = await companiesService.createCompany(companyData)
      commit('ADD_COMPANY', response.data)
      return response
    } catch (error) {
      console.error('Erro ao criar empresa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async updateCompany({ commit }, { id, companyData }) {
    try {
      commit('SET_LOADING', true)
      const response = await companiesService.updateCompany(id, companyData)
      commit('UPDATE_COMPANY', response.data)
      return response
    } catch (error) {
      console.error('Erro ao atualizar empresa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async deleteCompany({ commit }, id) {
    try {
      commit('SET_LOADING', true)
      await companiesService.deleteCompany(id)
      commit('REMOVE_COMPANY', id)
      return true
    } catch (error) {
      console.error('Erro ao excluir empresa:', error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  }
}

const getters = {
  companies: state => state.companies,
  currentCompany: state => state.currentCompany,
  loading: state => state.loading
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
