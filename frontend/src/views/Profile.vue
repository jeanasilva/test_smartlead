<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
      <!-- Hero Header -->
      <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black/10">
          <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div class="flex flex-col lg:flex-row items-center lg:items-start space-y-8 lg:space-y-0 lg:space-x-12">
            <!-- Avatar Section -->
            <div class="relative group">
              <div class="w-40 h-40 bg-white rounded-full flex items-center justify-center shadow-2xl border-4 border-white/80 backdrop-blur-sm transition-all duration-300 group-hover:scale-105 group-hover:shadow-3xl">
                <span class="text-5xl font-bold bg-gradient-to-br from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                  {{ user.name | getInitials }}
                </span>
              </div>
              <div class="absolute -bottom-3 -right-3 w-10 h-10 bg-emerald-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg animate-pulse">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>

            <!-- User Info -->
            <div class="text-center lg:text-left text-white flex-1">
              <h1 class="text-5xl lg:text-6xl font-bold mb-3 bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                {{ user.name }}
              </h1>
              <p class="text-xl lg:text-2xl text-blue-100 mb-6 font-light">{{ user.email }}</p>

              <div class="flex flex-wrap justify-center lg:justify-start gap-4 mb-8">
                <span class="inline-flex items-center px-5 py-3 rounded-full text-sm font-semibold bg-white/15 backdrop-blur-md border border-white/20 shadow-lg hover:bg-white/20 transition-all duration-300">
                  <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  {{ getRoleName(user.role) }}
                </span>
                <span class="inline-flex items-center px-5 py-3 rounded-full text-sm font-semibold bg-white/15 backdrop-blur-md border border-white/20 shadow-lg hover:bg-white/20 transition-all duration-300">
                  <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                  </svg>
                  {{ user.company?.name || 'Sem empresa' }}
                </span>
              </div>

              <!-- Quick Stats -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 shadow-xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                  <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">{{ stats.total_tasks }}</div>
                    <div class="text-sm text-blue-100 font-medium">Total de Tarefas</div>
                  </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 shadow-xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                  <div class="text-center">
                    <div class="text-3xl font-bold text-emerald-300 mb-1">{{ stats.completed_tasks }}</div>
                    <div class="text-sm text-blue-100 font-medium">Concluídas</div>
                  </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 shadow-xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                  <div class="text-center">
                    <div class="text-3xl font-bold text-amber-300 mb-1">{{ completionRate }}%</div>
                    <div class="text-sm text-blue-100 font-medium">Taxa de Conclusão</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Forms -->
          <div class="lg:col-span-2 space-y-8">

            <!-- Profile Information Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                  </svg>
                  Informações Pessoais
                </h3>
              </div>
              <form @submit.prevent="updateProfile" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                      </svg>
                      Nome completo *
                    </label>
                    <input
                      id="name"
                      v-model="profileForm.name"
                      type="text"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                      :class="{ 'border-red-300 focus:ring-red-500': profileErrors.name }"
                      placeholder="Digite seu nome completo"
                    >
                    <p v-if="profileErrors.name" class="text-sm text-red-600 flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                      </svg>
                      {{ profileErrors.name }}
                    </p>
                  </div>
                  <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                      </svg>
                      E-mail *
                    </label>
                    <input
                      id="email"
                      v-model="profileForm.email"
                      type="email"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                      :class="{ 'border-red-300 focus:ring-red-500': profileErrors.email }"
                      placeholder="seu@email.com"
                    >
                    <p v-if="profileErrors.email" class="text-sm text-red-600 flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                      </svg>
                      {{ profileErrors.email }}
                    </p>
                  </div>
                </div>
                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="profileLoading"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                  >
                    <svg v-if="profileLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    {{ profileLoading ? 'Atualizando...' : 'Atualizar Perfil' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                  </svg>
                  Alterar Senha
                </h3>
              </div>
              <form @submit.prevent="changePassword" class="p-6">
                <div class="space-y-2">
                  <label for="current_password" class="block text-sm font-medium text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    Senha atual *
                  </label>
                  <input
                    id="current_password"
                    v-model="passwordForm.current_password"
                    type="password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                    :class="{ 'border-red-300 focus:ring-red-500': passwordErrors.current_password }"
                    placeholder="Digite sua senha atual"
                  >
                  <p v-if="passwordErrors.current_password" class="text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ passwordErrors.current_password }}
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700 flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                      </svg>
                      Nova senha *
                    </label>
                    <input
                      id="password"
                      v-model="passwordForm.password"
                      type="password"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                      :class="{ 'border-red-300 focus:ring-red-500': passwordErrors.password }"
                      placeholder="Digite a nova senha"
                    >
                    <p v-if="passwordErrors.password" class="text-sm text-red-600 flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                      </svg>
                      {{ passwordErrors.password }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                      </svg>
                      Confirmar senha *
                    </label>
                    <input
                      id="password_confirmation"
                      v-model="passwordForm.password_confirmation"
                      type="password"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                      placeholder="Confirme a nova senha"
                    >
                  </div>
                </div>

                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="passwordLoading"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                  >
                    <svg v-if="passwordLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    {{ passwordLoading ? 'Alterando...' : 'Alterar Senha' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Right Column - Stats and Info -->
          <div class="space-y-8">
            <!-- Detailed Statistics Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Estatísticas Detalhadas
                </h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                  <!-- Total Tasks -->
                  <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-blue-600">Total de Tarefas</p>
                        <p class="text-2xl font-bold text-blue-900">{{ stats.total_tasks }}</p>
                      </div>
                      <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- Completed Tasks -->
                  <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-green-600">Concluídas</p>
                        <p class="text-2xl font-bold text-green-900">{{ stats.completed_tasks }}</p>
                      </div>
                      <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- In Progress Tasks -->
                  <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-xl border border-yellow-200">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-yellow-600">Em Andamento</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ stats.in_progress_tasks }}</p>
                      </div>
                      <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- Pending Tasks -->
                  <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-xl border border-red-200">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-red-600">Pendentes</p>
                        <p class="text-2xl font-bold text-red-900">{{ stats.pending_tasks }}</p>
                      </div>
                      <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Completion Rate -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">Taxa de Conclusão</h4>
                    <span class="text-3xl font-bold text-purple-600">{{ completionRate }}%</span>
                  </div>
                  <div class="w-full bg-purple-200 rounded-full h-3">
                    <div
                      class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-500 ease-out"
                      :style="{ width: completionRate + '%' }"
                    ></div>
                  </div>
                  <p class="text-sm text-gray-600 mt-2">
                    {{ stats.completed_tasks }} de {{ stats.total_tasks }} tarefas concluídas
                  </p>
                </div>
              </div>
            </div>

            <!-- Account Information Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                  </svg>
                  Informações da Conta
                </h3>
              </div>

              <div class="p-6 space-y-4">
                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                  <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Membro desde</p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ user.created_at | formatDate }}
                    </p>
                  </div>
                </div>
                <div v-if="user.last_login_at" class="flex items-center p-4 bg-gray-50 rounded-xl">
                  <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Último acesso</p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ user.last_login_at | formatDate }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                  <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Status da conta</p>
                    <p>
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                        :class="user.status === 'active'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'"
                      >
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ user.status === 'active' ? 'Ativa' : 'Inativa' }}
                      </span>
                    </p>
                  </div>
                </div>
                <div v-if="user.company" class="flex items-center p-4 bg-gray-50 rounded-xl">
                  <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Empresa</p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ user.company.name }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Danger Zone Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-red-200 overflow-hidden">
              <div class="bg-gradient-to-r from-red-500 to-red-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  Zona de Perigo
                </h3>
              </div>

              <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-red-800">
                        Ações irreversíveis
                      </h4>
                      <p class="text-sm text-red-700 mt-1">
                        As ações abaixo são irreversíveis. Prossiga com cautela.
                      </p>
                    </div>
                  </div>
                </div>
                <button
                  @click="confirmDeleteAccount"
                  class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                  </svg>
                  Excluir minha conta
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- FECHA min-h-screen bg-gray-50 -->
  </AppLayout>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import AppLayout from '@/components/layout/AppLayout.vue'

export default {
  name: 'Profile',
  components: {
    AppLayout
  },
  data() {
    return {
      profileForm: {
        name: '',
        email: ''
      },
      passwordForm: {
        current_password: '',
        password: '',
        password_confirmation: ''
      },
      profileErrors: {},
      passwordErrors: {},
      profileLoading: false,
      passwordLoading: false,
      stats: {
        total_tasks: 0,
        completed_tasks: 0,
        in_progress_tasks: 0,
        pending_tasks: 0
      }
    }
  },
  computed: {
    ...mapGetters('auth', ['user']),
    
    completionRate() {
      if (this.stats.total_tasks === 0) return 0
      return Math.round((this.stats.completed_tasks / this.stats.total_tasks) * 100)
    }
  },
  async created() {
    await this.loadProfile()
    this.initForms()
  },
  methods: {
    ...mapActions('auth', ['updateUserData']),
    
    initForms() {
      this.profileForm = {
        name: this.user.name || '',
        email: this.user.email || ''
      }
    },

    async loadProfile() {
      try {
        const response = await this.$http.get('/profile')
        const { user, stats } = response.data
        
        // Atualizar dados do usuário no Vuex
        await this.updateUserData(user)
        this.stats = stats
      } catch (error) {
        console.error('Erro ao carregar perfil:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Não foi possível carregar os dados do perfil.',
          icon: 'error'
        })
      }
    },

    async loadStats() {
      try {
        const response = await this.$http.get('/profile/stats')
        this.stats = response.data
      } catch (error) {
        console.error('Erro ao carregar estatísticas:', error)
      }
    },

    async updateProfile() {
      this.profileErrors = {}
      this.profileLoading = true

      try {
        const response = await this.$http.put('/profile', this.profileForm)
        
        // Atualizar dados do usuário no Vuex
        await this.updateUserData(response.data.user)

        this.$swal.fire({
          title: 'Perfil atualizado!',
          text: 'Suas informações foram atualizadas com sucesso.',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })

        // Recarregar dados do perfil
        await this.loadProfile()
      } catch (error) {
        console.error('Erro ao atualizar perfil:', error)

        if (error.response?.data?.errors) {
          this.profileErrors = error.response.data.errors
        } else {
          this.$swal.fire({
            title: 'Erro',
            text: 'Não foi possível atualizar o perfil.',
            icon: 'error'
          })
        }
      } finally {
        this.profileLoading = false
      }
    },

    async changePassword() {
      this.passwordErrors = {}
      this.passwordLoading = true

      try {
        await this.$http.put('/profile/password', this.passwordForm)

        this.passwordForm = {
          current_password: '',
          password: '',
          password_confirmation: ''
        }

        this.$swal.fire({
          title: 'Senha alterada!',
          text: 'Sua senha foi alterada com sucesso.',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
      } catch (error) {
        console.error('Erro ao alterar senha:', error)

        if (error.response?.data?.errors) {
          this.passwordErrors = error.response.data.errors
        } else {
          this.$swal.fire({
            title: 'Erro',
            text: 'Não foi possível alterar a senha.',
            icon: 'error'
          })
        }
      } finally {
        this.passwordLoading = false
      }
    },

    async confirmDeleteAccount() {
      const result = await this.$swal.fire({
        title: 'Confirmar exclusão da conta',
        text: 'Esta ação não pode ser desfeita. Sua conta e todos os seus dados serão excluídos permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir minha conta',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        input: 'text',
        inputPlaceholder: 'Digite "EXCLUIR" para confirmar',
        inputValidator: (value) => {
          if (value !== 'EXCLUIR') {
            return 'Digite "EXCLUIR" para confirmar'
          }
        }
      })

      if (result.isConfirmed) {
        try {
          await this.$http.delete('/profile')
          
          this.$swal.fire({
            title: 'Conta excluída',
            text: 'Sua conta foi excluída com sucesso.',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
          })

          // Fazer logout e redirecionar
          this.$store.dispatch('auth/logout')
          this.$router.push('/login')
        } catch (error) {
          console.error('Erro ao excluir conta:', error)
          this.$swal.fire({
            title: 'Erro',
            text: 'Não foi possível excluir a conta.',
            icon: 'error'
          })
        }
      }
    },

    getRoleName(role) {
      const roles = {
        admin: 'Administrador',
        manager: 'Gerente', 
        user: 'Usuário'
      }
      return roles[role] || role
    },

    getRoleClass(role) {
      const classes = {
        admin: 'bg-purple-100 text-purple-800',
        manager: 'bg-blue-100 text-blue-800',
        user: 'bg-gray-100 text-gray-800'
      }
      return classes[role] || 'bg-gray-100 text-gray-800'
    }
  },
  filters: {
    getInitials(name) {
      if (!name) return ''
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substr(0, 2)
        .toUpperCase()
    }
  }
}
</script>

<style scoped>
/* Melhorar a aparência geral da página */
.profile-page {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

/* Melhorar os cards */
.profile-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.profile-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Melhorar os headers dos cards */
.card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 16px 16px 0 0;
}

/* Melhorar os inputs */
.form-input {
  border-radius: 12px;
  border: 2px solid #e5e7eb;
  transition: all 0.3s ease;
  font-size: 16px;
}
.form-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  transform: scale(1.02);
}

/* Melhorar os botões */
.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}

.btn-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.3);
}

.btn-warning:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.btn-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.3);
}

.btn-danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

/* Melhorar as estatísticas */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 24px;
}

.stat-card {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #cbd5e1;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 500;
}

/* Animações */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Loading spinner */
.spinner {
  border: 2px solid #f3f3f3;
  border-top: 2px solid #3498db;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsividade */
@media (max-width: 768px) {
  .profile-page {
    padding: 16px;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .stat-card {
    padding: 16px;
  }

  .stat-number {
    font-size: 2rem;
  }
}
</style>
