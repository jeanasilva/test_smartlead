import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './App.vue'
import router from './router'
import store from './store'
import './assets/css/main.css'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

Vue.config.productionTip = false

Vue.use(VueRouter)
Vue.use(VueSweetalert2)

// Filtros globais
Vue.filter('formatDate', function (value) {
  if (!value) return ''
  const date = new Date(value)
  return date.toLocaleDateString('pt-BR')
})

Vue.filter('formatDateTime', function (value) {
  if (!value) return ''
  const date = new Date(value)
  return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
})

Vue.filter('capitalize', function (value) {
  if (!value) return ''
  value = value.toString()
  return value.charAt(0).toUpperCase() + value.slice(1)
})

// Configurar Axios
import api from '@/services/api'
Vue.prototype.$http = api

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
