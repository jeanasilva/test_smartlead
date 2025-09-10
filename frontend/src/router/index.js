import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'

// Importar views
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import Dashboard from '@/views/Dashboard.vue'
import Tasks from '@/views/Tasks.vue'
import Companies from '@/views/Companies.vue'
import Profile from '@/views/Profile.vue'
import Reports from '@/views/Reports.vue'
import Management from '@/views/Management.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: Tasks,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/companies',
    name: 'Companies',
    component: Companies,
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Reports,
    meta: { requiresAuth: true }
  },
  {
    path: '/management',
    name: 'Management',
    component: Management,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

// Guard de autenticação
router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters['auth/isAuthenticated']
  const isAdmin = store.getters['auth/isAdmin']
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)
  const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
  
  // Debug para verificar o estado
  console.log('Navigation Guard:', {
    to: to.path,
    from: from.path,
    isAuthenticated,
    isAdmin,
    requiresAuth,
    requiresGuest,
    requiresAdmin
  })
  
  if (requiresAuth && !isAuthenticated) {
    // Use replace instead of push for auth redirects
    next({ path: '/login', replace: true })
  } else if (requiresGuest && isAuthenticated) {
    next({ path: '/', replace: true })
  } else if (requiresAdmin && !isAdmin) {
    next({ path: '/', replace: true })
  } else {
    next()
  }
})

export default router
