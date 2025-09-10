import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import tasks from './modules/tasks'
import companies from './modules/companies'
import ui from './modules/ui'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    auth,
    tasks,
    companies,
    ui
  },
  strict: process.env.NODE_ENV !== 'production'
})
