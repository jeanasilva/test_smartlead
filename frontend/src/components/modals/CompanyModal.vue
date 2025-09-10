<template>
  <div v-if="isVisible" class="modal-overlay" :style="modalOverlayStyle" @click="handleBackdropClick">
    <div class="modal-container" :style="modalContainerStyle" @click.stop>
      <div class="modal-header" :style="modalHeaderStyle">
        <div class="modal-title-section">
          <h2 class="modal-title">
            <i class="fas fa-building"></i>
            {{ isEdit ? 'Editar Empresa' : 'Nova Empresa' }}
          </h2>
          <p class="modal-subtitle">
            {{ isEdit ? 'Atualize as informações da empresa' : 'Preencha os dados da nova empresa' }}
          </p>
        </div>
        <button @click="$emit('close')" class="modal-close-btn" :style="modalCloseBtnStyle">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body" :style="modalBodyStyle">
        <form @submit.prevent="handleSubmit" class="company-form">
          <div class="form-grid" :style="formGridStyle">
            <!-- Nome -->
            <div class="form-group full-width">
              <label for="name" class="form-label">
                <i class="fas fa-building"></i>
                Nome da Empresa *
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-input"
                :style="formInputStyle"
                placeholder="Digite o nome da empresa"
                required
                :disabled="loading"
              />
              <div v-if="errors.name" class="form-error">{{ errors.name[0] }}</div>
            </div>

            <!-- Identificador -->
            <div class="form-group full-width">
              <label for="identifier" class="form-label">
                <i class="fas fa-tag"></i>
                Identificador *
              </label>
              <input
                id="identifier"
                v-model="form.identifier"
                type="text"
                class="form-input"
                :style="formInputStyle"
                placeholder="Digite o identificador da empresa"
                required
                :disabled="loading"
              />
              <div v-if="errors.identifier" class="form-error">{{ errors.identifier[0] }}</div>
            </div>

            <!-- Descrição -->
            <div class="form-group full-width">
              <label for="description" class="form-label">
                <i class="fas fa-sticky-note"></i>
                Descrição
              </label>
              <textarea
                id="description"
                v-model="form.description"
                class="form-textarea"
                :style="formTextareaStyle"
                rows="4"
                placeholder="Digite a descrição da empresa..."
                :disabled="loading"
              ></textarea>
              <div v-if="errors.description" class="form-error">{{ errors.description[0] }}</div>
            </div>
          </div>
        </form>
      </div>
      
      <div class="modal-footer" :style="modalFooterStyle">
        <button
          @click="$emit('close')"
          type="button"
          class="btn-secondary"
          :style="btnSecondaryStyle"
          :disabled="loading"
        >
          <i class="fas fa-times"></i>
          Cancelar
        </button>
        
        <button
          @click="handleSubmit"
          type="submit"
          class="btn-primary"
          :style="btnPrimaryStyle"
          :disabled="loading || !isFormValid"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin"></i>
          <i v-else :class="isEdit ? 'fas fa-save' : 'fas fa-plus'"></i>
          {{ loading ? 'Salvando...' : (isEdit ? 'Atualizar' : 'Criar Empresa') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CompanyModal',
  props: {
    isVisible: {
      type: Boolean,
      default: false
    },
    company: {
      type: Object,
      default: null
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      form: {
        name: '',
        identifier: '',
        description: ''
      },
      errors: {}
    }
  },
  computed: {
    isEdit() {
      return this.company && this.company.id
    },
    isFormValid() {
      return this.form.name.trim().length > 0 && this.form.identifier.trim().length > 0
    },
    modalOverlayStyle() {
      return {
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: 1000,
        padding: '20px'
      }
    },
    modalContainerStyle() {
      return {
        backgroundColor: 'white',
        borderRadius: '12px',
        boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
        width: '100%',
        maxWidth: '800px',
        maxHeight: '90vh',
        overflow: 'hidden',
        display: 'flex',
        flexDirection: 'column'
      }
    },
    modalHeaderStyle() {
      return {
        display: 'flex',
        alignItems: 'flex-start',
        justifyContent: 'space-between',
        padding: '24px',
        borderBottom: '1px solid #f3f4f6',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white'
      }
    },
    modalBodyStyle() {
      return {
        flex: 1,
        overflowY: 'auto',
        padding: '24px'
      }
    },
    modalFooterStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'flex-end',
        gap: '12px',
        padding: '24px',
        borderTop: '1px solid #f3f4f6',
        backgroundColor: '#f9fafb'
      }
    },
    modalCloseBtnStyle() {
      return {
        width: '40px',
        height: '40px',
        border: 'none',
        borderRadius: '50%',
        backgroundColor: 'rgba(255, 255, 255, 0.2)',
        color: 'white',
        cursor: 'pointer',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        transition: 'all 0.3s ease',
        fontSize: '16px'
      }
    },
    formGridStyle() {
      return {
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
        gap: '20px'
      }
    },
    formInputStyle() {
      return {
        width: '100%',
        padding: '12px 16px',
        border: '2px solid #e5e7eb',
        borderRadius: '8px',
        fontSize: '14px',
        transition: 'all 0.3s ease',
        backgroundColor: 'white'
      }
    },
    formSelectStyle() {
      return {
        width: '100%',
        padding: '12px 16px',
        border: '2px solid #e5e7eb',
        borderRadius: '8px',
        fontSize: '14px',
        transition: 'all 0.3s ease',
        backgroundColor: 'white',
        cursor: 'pointer'
      }
    },
    formTextareaStyle() {
      return {
        width: '100%',
        padding: '12px 16px',
        border: '2px solid #e5e7eb',
        borderRadius: '8px',
        fontSize: '14px',
        transition: 'all 0.3s ease',
        backgroundColor: 'white',
        resize: 'vertical',
        minHeight: '100px'
      }
    },
    btnPrimaryStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 24px',
        background: this.loading || !this.isFormValid 
          ? '#d1d5db' 
          : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        border: 'none',
        borderRadius: '8px',
        cursor: this.loading || !this.isFormValid ? 'not-allowed' : 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        transition: 'all 0.3s ease'
      }
    },
    btnSecondaryStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 24px',
        background: 'white',
        color: '#6b7280',
        border: '2px solid #e5e7eb',
        borderRadius: '8px',
        cursor: this.loading ? 'not-allowed' : 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        transition: 'all 0.3s ease'
      }
    }
  },
  watch: {
    isVisible: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.resetForm()
          if (this.company) {
            this.loadCompanyData()
          }
          this.$nextTick(() => {
            document.body.style.overflow = 'hidden'
          })
        } else {
          document.body.style.overflow = ''
        }
      }
    }
  },
  methods: {
    resetForm() {
      this.form = {
        name: '',
        identifier: '',
        description: ''
      }
      this.errors = {}
    },
    
    loadCompanyData() {
      if (this.company) {
        this.form = {
          name: this.company.name || '',
          identifier: this.company.identifier || '',
          description: this.company.description || ''
        }
      }
    },
    
    handleBackdropClick(event) {
      if (event.target === event.currentTarget) {
        this.$emit('close')
      }
    },
    
    handleSubmit() {
      if (!this.isFormValid || this.loading) {
        return
      }
      
      this.errors = {}
      
      // Basic validation
      if (!this.form.name.trim()) {
        this.errors.name = ['O nome da empresa é obrigatório']
        return
      }
      
      if (!this.form.identifier.trim()) {
        this.errors.identifier = ['O identificador da empresa é obrigatório']
        return
      }
      
      // Emit the form data
      this.$emit('submit', { ...this.form })
    }
  },
  
  beforeDestroy() {
    document.body.style.overflow = ''
  }
}
</script>

<style scoped>
.modal-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 4px 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.modal-subtitle {
  font-size: 14px;
  opacity: 0.9;
  margin: 0;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input:disabled,
.form-select:disabled,
.form-textarea:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: #f9fafb;
}

.form-error {
  color: #dc2626;
  font-size: 13px;
  margin-top: 6px;
}

.modal-close-btn:hover {
  background-color: rgba(255, 255, 255, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-secondary:hover:not(:disabled) {
  border-color: #d1d5db;
  background-color: #f9fafb;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .modal-container {
    margin: 10px;
    max-height: calc(100vh - 20px);
  }
  
  .form-grid {
    grid-template-columns: 1fr !important;
  }
  
  .modal-header {
    padding: 20px;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .modal-footer {
    padding: 20px;
    flex-direction: column-reverse;
    gap: 12px;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
}

/* Scrollbar Styling */
.modal-body::-webkit-scrollbar {
  width: 6px;
}

.modal-body::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
