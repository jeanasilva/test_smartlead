<template>
  <AppLayout
    page-title="Empresas"
    page-description="Gerencie suas empresas e acompanhe as informações"
    :breadcrumbs="breadcrumbs"
  >
    <template v-slot:pageActions>
      <button @click="openCompanyModal()" class="create-btn" :style="createBtnStyle">
        <i class="fas fa-plus"></i>
        Nova Empresa
      </button>
    </template>

    <!-- Filters Card -->
    <div class="filters-card" :style="cardStyle">
      <div class="card-header" :style="cardHeaderStyle">
        <h3 class="card-title">
          <i class="fas fa-filter"></i>
          Filtros
        </h3>
        <button @click="clearFilters" class="clear-filters-btn" :style="clearFiltersBtnStyle">
          <i class="fas fa-times"></i>
          Limpar
        </button>
      </div>
      <div class="filters-grid" :style="filtersGridStyle">
        <div class="filter-group">
          <label class="filter-label">Status</label>
          <select v-model="filters.status" @change="loadCompanies" class="filter-select" :style="filterSelectStyle">
            <option value="">Todos os Status</option>
            <option value="ativa">Ativa</option>
            <option value="inativa">Inativa</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label class="filter-label">Buscar</label>
          <div class="search-input-container" :style="searchInputContainerStyle">
            <i class="fas fa-search search-icon"></i>
            <input
              v-model="filters.search"
              @input="debounceSearch"
              type="text"
              placeholder="Buscar por nome ou CNPJ..."
              class="search-input"
              :style="searchInputStyle"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Companies Grid/List -->
    <div class="companies-container" :style="cardStyle">
      <div class="companies-header" :style="cardHeaderStyle">
        <div class="companies-count">
          <h3 class="card-title">
            <i class="fas fa-building"></i>
            Empresas ({{ pagination.total }})
          </h3>
        </div>
        <div class="view-controls" :style="viewControlsStyle">
          <button 
            @click="viewMode = 'grid'"
            :class="{ 'active': viewMode === 'grid' }"
            class="view-btn"
            :style="getViewBtnStyle('grid')"
          >
            <i class="fas fa-th"></i>
          </button>
          <button 
            @click="viewMode = 'list'"
            :class="{ 'active': viewMode === 'list' }"
            class="view-btn"
            :style="getViewBtnStyle('list')"
          >
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-container" :style="loadingContainerStyle">
        <SkeletonLoader 
          :type="viewMode === 'grid' ? 'card' : 'list'" 
          :items="6"
        />
      </div>

      <!-- Companies Content -->
      <div v-else-if="companies.length > 0" class="companies-content">
        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="companies-grid" :style="companiesGridStyle">
          <div 
            v-for="company in companies" 
            :key="company.id"
            class="company-card"
            :style="getCompanyCardStyle(company.status)"
            @click="openCompanyModal(company)"
          >
            <div class="company-card-header">
              <div class="company-avatar" :style="companyAvatarStyle">
                {{ getCompanyInitials(company.name) }}
              </div>
              <div class="company-actions">
                <button @click.stop="openCompanyModal(company)" class="company-action-btn" :style="companyActionBtnStyle">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click.stop="deleteCompany(company)" class="company-action-btn delete" :style="companyActionBtnStyle">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
            
            <div class="company-card-body">
              <h4 class="company-name">{{ company.name }}</h4>
              <p class="company-cnpj" v-if="company.cnpj">
                <i class="fas fa-id-card"></i>
                {{ formatCNPJ(company.cnpj) }}
              </p>
              <p class="company-email" v-if="company.email">
                <i class="fas fa-envelope"></i>
                {{ company.email }}
              </p>
              <p class="company-phone" v-if="company.phone">
                <i class="fas fa-phone"></i>
                {{ company.phone }}
              </p>
              
              <div class="company-meta" :style="companyMetaStyle">
                <div class="company-tasks">
                  <i class="fas fa-tasks"></i>
                  {{ company.tasks_count || 0 }} tarefas
                </div>
                <div class="company-date">
                  <i class="fas fa-calendar"></i>
                  {{ formatDate(company.created_at) }}
                </div>
              </div>
            </div>
            
            <div class="company-card-footer">
              <div class="company-status" :style="getCompanyStatusStyle(company.status)">
                {{ getStatusLabel(company.status) }}
              </div>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="companies-list" :style="companiesListStyle">
          <div 
            v-for="company in companies" 
            :key="company.id"
            class="company-list-item"
            :style="companyListItemStyle"
            @click="openCompanyModal(company)"
          >
            <div class="company-list-content">
              <div class="company-list-main">
                <div class="company-avatar-small" :style="companyAvatarSmallStyle">
                  {{ getCompanyInitials(company.name) }}
                </div>
                <div class="company-info">
                  <h4 class="company-name">{{ company.name }}</h4>
                  <p class="company-details">
                    <span v-if="company.cnpj">{{ formatCNPJ(company.cnpj) }}</span>
                    <span v-if="company.email"> • {{ company.email }}</span>
                  </p>
                </div>
              </div>
              
              <div class="company-list-meta" :style="companyListMetaStyle">
                <div class="company-tasks">
                  <i class="fas fa-tasks"></i>
                  {{ company.tasks_count || 0 }} tarefas
                </div>
                <div class="company-status" :style="getCompanyStatusStyle(company.status)">
                  {{ getStatusLabel(company.status) }}
                </div>
                <div class="company-date">
                  {{ formatDate(company.created_at) }}
                </div>
              </div>
            </div>
            
            <div class="company-list-actions">
              <button @click.stop="openCompanyModal(company)" class="company-action-btn" :style="companyActionBtnStyle">
                <i class="fas fa-edit"></i>
              </button>
              <button @click.stop="deleteCompany(company)" class="company-action-btn delete" :style="companyActionBtnStyle">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="empty-state" :style="emptyStateStyle">
        <div class="empty-icon">
          <i class="fas fa-building"></i>
        </div>
        <h3>Nenhuma empresa encontrada</h3>
        <p>{{ hasFilters ? 'Nenhuma empresa corresponde aos filtros aplicados.' : 'Você ainda não cadastrou nenhuma empresa.' }}</p>
        <button @click="openCompanyModal()" class="empty-action-btn" :style="emptyActionBtnStyle">
          <i class="fas fa-plus"></i>
          Cadastrar primeira empresa
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="companies.length > 0 && pagination.last_page > 1" class="pagination-container" :style="paginationContainerStyle">
        <div class="pagination-info">
          Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} empresas
        </div>
        <div class="pagination-controls" :style="paginationControlsStyle">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="pagination-btn"
            :style="paginationBtnStyle"
          >
            Anterior
          </button>
          <span class="pagination-current">
            Página {{ pagination.current_page }} de {{ pagination.last_page }}
          </span>
          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="pagination-btn"
            :style="paginationBtnStyle"
          >
            Próxima
          </button>
        </div>
      </div>
    </div>

    <!-- Company Modal -->
    <CompanyModal
      :is-visible="showCompanyModal"
      :company="selectedCompany"
      :loading="modalLoading"
      @close="closeCompanyModal"
      @submit="handleCompanySaved"
    />
  </AppLayout>
</template>

<script>
import AppLayout from '@/components/layout/AppLayout.vue'
import SkeletonLoader from '@/components/common/SkeletonLoader.vue'
import CompanyModal from '@/components/modals/CompanyModal.vue'
import { companyService } from '@/services/api'

export default {
  name: 'Companies',
  components: {
    AppLayout,
    SkeletonLoader,
    CompanyModal
  },
  data() {
    return {
      loading: false,
      viewMode: 'grid', // 'grid' or 'list'
      showCompanyModal: false,
      selectedCompany: null,
      modalLoading: false,
      companies: [],
      filters: {
        status: '',
        search: ''
      },
      pagination: {
        current_page: 1,
        per_page: 12,
        total: 0,
        last_page: 1,
        from: 0,
        to: 0
      },
      searchTimeout: null,
      breadcrumbs: [
        { label: 'Dashboard', path: '/' },
        { label: 'Empresas', path: '/companies' }
      ]
    }
  },
  computed: {
    hasFilters() {
      return this.filters.search || this.filters.status
    },
    cardStyle() {
      return {
        background: 'white',
        borderRadius: '12px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
        border: '1px solid #e5e7eb',
        marginBottom: '24px'
      }
    },
    cardHeaderStyle() {
      return {
        padding: '20px 24px',
        borderBottom: '1px solid #f3f4f6',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between'
      }
    },
    filtersGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
        gap: '20px',
        padding: '24px'
      }
    },
    filterSelectStyle() {
      return {
        width: '100%',
        padding: '8px 12px',
        border: '1px solid #d1d5db',
        borderRadius: '6px',
        fontSize: '14px'
      }
    },
    searchInputContainerStyle() {
      return {
        position: 'relative'
      }
    },
    searchInputStyle() {
      return {
        width: '100%',
        padding: '8px 12px 8px 40px',
        border: '1px solid #d1d5db',
        borderRadius: '6px',
        fontSize: '14px'
      }
    },
    viewControlsStyle() {
      return {
        display: 'flex',
        gap: '4px',
        background: '#f8fafc',
        borderRadius: '6px',
        padding: '4px'
      }
    },
    companiesGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))',
        gap: '24px',
        padding: '24px'
      }
    },
    companiesListStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        gap: '16px',
        padding: '24px'
      }
    },
    companyListItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        padding: '20px',
        background: '#f9fafb',
        border: '1px solid #e5e7eb',
        borderRadius: '8px',
        cursor: 'pointer',
        transition: 'all 0.3s ease'
      }
    },
    companyListMetaStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '24px',
        fontSize: '13px',
        color: '#6b7280'
      }
    },
    companyMetaStyle() {
      return {
        display: 'flex',
        flexDirection: 'column',
        gap: '8px',
        fontSize: '13px',
        color: '#6b7280',
        margin: '12px 0'
      }
    },
    companyActionBtnStyle() {
      return {
        width: '32px',
        height: '32px',
        border: 'none',
        borderRadius: '6px',
        cursor: 'pointer',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '14px',
        transition: 'all 0.3s ease',
        backgroundColor: '#f3f4f6',
        color: '#6b7280'
      }
    },
    companyAvatarStyle() {
      return {
        width: '56px',
        height: '56px',
        borderRadius: '50%',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '20px',
        fontWeight: '600'
      }
    },
    companyAvatarSmallStyle() {
      return {
        width: '48px',
        height: '48px',
        borderRadius: '50%',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '16px',
        fontWeight: '600',
        marginRight: '16px'
      }
    },
    emptyStateStyle() {
      return {
        textAlign: 'center',
        padding: '60px 20px',
        color: '#6b7280'
      }
    },
    loadingContainerStyle() {
      return {
        padding: '24px'
      }
    },
    paginationContainerStyle() {
      return {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: '24px',
        borderTop: '1px solid #f3f4f6',
        fontSize: '14px',
        color: '#6b7280'
      }
    },
    paginationControlsStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '16px'
      }
    },
    paginationBtnStyle() {
      return {
        padding: '8px 12px',
        border: '1px solid #d1d5db',
        borderRadius: '6px',
        background: 'white',
        cursor: 'pointer',
        transition: 'all 0.3s ease'
      }
    },
    createBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 20px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        border: 'none',
        borderRadius: '8px',
        cursor: 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        transition: 'all 0.3s ease',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)'
      }
    },
    clearFiltersBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '6px',
        padding: '8px 12px',
        background: '#f3f4f6',
        color: '#6b7280',
        border: 'none',
        borderRadius: '6px',
        cursor: 'pointer',
        fontSize: '13px',
        transition: 'all 0.3s ease'
      }
    },
    emptyActionBtnStyle() {
      return {
        display: 'inline-flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 24px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        border: 'none',
        borderRadius: '8px',
        cursor: 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        marginTop: '20px',
        transition: 'all 0.3s ease'
      }
    }
  },
  methods: {
    async loadCompanies() {
      this.loading = true
      try {
        const params = {
          page: this.pagination.current_page,
          per_page: this.pagination.per_page,
          ...this.filters
        }
        
        // Remove empty filters
        Object.keys(params).forEach(key => {
          if (params[key] === '' || params[key] === null || params[key] === undefined) {
            delete params[key]
          }
        })
        
        const response = await companyService.list(params)
        
        this.companies = response.data.data || []
        this.pagination = {
          current_page: response.data.current_page || 1,
          per_page: response.data.per_page || 12,
          total: response.data.total || 0,
          last_page: response.data.last_page || 1,
          from: response.data.from || 0,
          to: response.data.to || 0
        }
      } catch (error) {
        console.error('Erro ao carregar empresas:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Erro ao carregar as empresas',
          icon: 'error'
        })
      } finally {
        this.loading = false
      }
    },
    
    openCompanyModal(company = null) {
      this.selectedCompany = company
      this.showCompanyModal = true
    },
    
    closeCompanyModal() {
      this.showCompanyModal = false
      this.selectedCompany = null
    },
    
    async handleCompanySaved(formData) {
      this.modalLoading = true
      
      try {
        if (this.selectedCompany?.id) {
          await companyService.update(this.selectedCompany.id, formData)
        } else {
          await companyService.create(formData)
        }
        
        this.$swal.fire({
          title: 'Sucesso!',
          text: this.selectedCompany?.id ? 'Empresa atualizada com sucesso!' : 'Empresa cadastrada com sucesso!',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        })
        
        this.closeCompanyModal()
        await this.loadCompanies()
        
      } catch (error) {
        console.error('Erro ao salvar empresa:', error)
        this.$swal.fire({
          title: 'Erro',
          text: 'Erro ao salvar empresa',
          icon: 'error'
        })
      } finally {
        this.modalLoading = false
      }
    },
    
    async deleteCompany(company) {
      const result = await this.$swal.fire({
        title: 'Excluir Empresa',
        text: `Tem certeza que deseja excluir a empresa "${company.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
      })
      
      if (result.isConfirmed) {
        try {
          await companyService.delete(company.id)
          this.$swal.fire({
            title: 'Sucesso!',
            text: 'Empresa excluída com sucesso',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          })
          this.loadCompanies()
        } catch (error) {
          console.error('Erro ao excluir empresa:', error)
          this.$swal.fire({
            title: 'Erro',
            text: 'Erro ao excluir a empresa',
            icon: 'error'
          })
        }
      }
    },
    
    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1
        this.loadCompanies()
      }, 500)
    },
    
    clearFilters() {
      this.filters = {
        status: '',
        search: ''
      }
      this.pagination.current_page = 1
      this.loadCompanies()
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page
        this.loadCompanies()
      }
    },
    
    getViewBtnStyle(mode) {
      const isActive = this.viewMode === mode
      return {
        padding: '8px 12px',
        border: 'none',
        borderRadius: '4px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        fontSize: '14px',
        backgroundColor: isActive ? 'white' : 'transparent',
        color: isActive ? '#374151' : '#6b7280',
        boxShadow: isActive ? '0 1px 2px rgba(0, 0, 0, 0.1)' : 'none'
      }
    },
    
    getCompanyCardStyle(status) {
      const statusColors = {
        ativa: '#f0f9ff',
        inativa: '#fef2f2'
      }
      
      return {
        background: statusColors[status] || '#ffffff',
        border: '1px solid #e5e7eb',
        borderRadius: '12px',
        padding: '20px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        height: '100%',
        display: 'flex',
        flexDirection: 'column'
      }
    },
    
    getCompanyStatusStyle(status) {
      const styles = {
        ativa: { background: '#dcfce7', color: '#166534' },
        inativa: { background: '#fee2e2', color: '#991b1b' }
      }
      
      return {
        ...styles[status] || styles.ativa,
        padding: '4px 12px',
        borderRadius: '20px',
        fontSize: '12px',
        fontWeight: '600'
      }
    },
    
    getStatusLabel(status) {
      return {
        ativa: 'Ativa',
        inativa: 'Inativa'
      }[status] || 'Ativa'
    },
    
    getCompanyInitials(name) {
      if (!name) return '??'
      return name.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase()
    },
    
    formatCNPJ(cnpj) {
      if (!cnpj) return ''
      return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5')
    },
    
    formatDate(date) {
      return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    }
  },
  
  async mounted() {
    await this.loadCompanies()
  }
}
</script>

<style scoped>
.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.filter-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  z-index: 1;
}

.filter-select:focus,
.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.company-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.company-cnpj,
.company-email,
.company-phone {
  font-size: 14px;
  color: #6b7280;
  margin: 4px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.company-details {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.company-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.company-actions {
  display: flex;
  gap: 6px;
}

.company-card-body {
  flex: 1;
}

.company-card-footer {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}

.company-tasks,
.company-date {
  display: flex;
  align-items: center;
  gap: 6px;
}

.company-list-content {
  flex: 1;
  display: flex;
  align-items: center;
}

.company-list-main {
  display: flex;
  align-items: center;
  flex: 1;
}

.company-list-actions {
  display: flex;
  gap: 8px;
}

.company-list-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.company-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

.company-action-btn:hover {
  background-color: #e5e7eb !important;
  color: #374151 !important;
}

.company-action-btn.delete:hover {
  background-color: #fee2e2 !important;
  color: #dc2626 !important;
}

.empty-icon i {
  font-size: 64px;
  color: #d1d5db;
  margin-bottom: 24px;
}

.empty-state h3 {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
  margin: 0 0 8px 0;
}

.empty-state p {
  margin: 0 0 24px 0;
  color: #6b7280;
}

.create-btn:hover,
.pagination-btn:hover,
.clear-filters-btn:hover,
.empty-action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .filters-grid {
    grid-template-columns: 1fr !important;
  }
  
  .companies-grid {
    grid-template-columns: 1fr !important;
  }
  
  .company-list-item {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }
  
  .company-list-actions {
    justify-content: center;
  }
  
  .pagination-container {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
}
</style>
