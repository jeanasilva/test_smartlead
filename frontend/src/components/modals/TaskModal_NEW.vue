<template>
  <div v-if="isVisible" class="modal-overlay" :style="overlayStyle" @click="closeModal">
    <div class="modal-container" :style="modalStyle" @click.stop>
      <div class="modal-header" :style="headerStyle">
        <h2 class="modal-title">
          <i class="fas fa-tasks"></i>
          {{ task && task.id ? 'Editar Tarefa' : 'Nova Tarefa' }}
        </h2>
        <button @click="closeModal" class="close-btn" :style="closeBtnStyle">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form @submit.prevent="submitForm" class="modal-body" :style="bodyStyle">
        <!-- Título -->
        <div class="form-group">
          <label for="title" class="form-label">
            <i class="fas fa-heading"></i>
            Título *
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="form-input"
            :style="inputStyle"
            :class="{ 'error': errors.title }"
            placeholder="Digite o título da tarefa"
          />
          <div v-if="errors.title" class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            {{ errors.title[0] }}
          </div>
        </div>

        <!-- Descrição -->
        <div class="form-group">
          <label for="description" class="form-label">
            <i class="fas fa-align-left"></i>
            Descrição
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="form-input"
            :style="textareaStyle"
            :class="{ 'error': errors.description }"
            placeholder="Digite a descrição da tarefa"
          ></textarea>
          <div v-if="errors.description" class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            {{ errors.description[0] }}
          </div>
        </div>

        <!-- Status e Prioridade -->
        <div class="form-row">
          <div class="form-group">
            <label for="status" class="form-label">
              <i class="fas fa-flag"></i>
              Status *
            </label>
            <select
              id="status"
              v-model="form.status"
              required
              class="form-input"
              :style="selectStyle"
              :class="{ 'error': errors.status }"
            >
              <option value="">Selecione o status</option>
              <option value="pendente">Pendente</option>
              <option value="em_andamento">Em Andamento</option>
              <option value="concluida">Concluída</option>
            </select>
            <div v-if="errors.status" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.status[0] }}
            </div>
          </div>

          <div class="form-group">
            <label for="priority" class="form-label">
              <i class="fas fa-exclamation-triangle"></i>
              Prioridade *
            </label>
            <select
              id="priority"
              v-model="form.priority"
              required
              class="form-input"
              :style="selectStyle"
              :class="{ 'error': errors.priority }"
            >
              <option value="">Selecione a prioridade</option>
              <option value="baixa">Baixa</option>
              <option value="media">Média</option>
              <option value="alta">Alta</option>
            </select>
            <div v-if="errors.priority" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.priority[0] }}
            </div>
          </div>
        </div>

        <!-- Responsável e Prazo -->
        <div class="form-row">
          <div class="form-group">
            <label for="user_id" class="form-label">
              <i class="fas fa-user"></i>
              Responsável
            </label>
            <select
              id="user_id"
              v-model="form.user_id"
              class="form-input"
              :style="selectStyle"
              :class="{ 'error': errors.user_id }"
            >
              <option value="">Atribuir a alguém (opcional)</option>
              <option 
                v-for="user in users" 
                :key="user.id" 
                :value="user.id"
              >
                {{ user.name }}
              </option>
            </select>
            <div v-if="errors.user_id" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.user_id[0] }}
            </div>
          </div>

          <div class="form-group">
            <label for="due_date" class="form-label">
              <i class="fas fa-calendar-alt"></i>
              Prazo
            </label>
            <input
              id="due_date"
              v-model="form.due_date"
              type="date"
              class="form-input"
              :style="inputStyle"
              :class="{ 'error': errors.due_date }"
              :min="minDate"
            />
            <div v-if="errors.due_date" class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ errors.due_date[0] }}
            </div>
          </div>
        </div>

        <!-- Botões -->
        <div class="form-actions" :style="actionsStyle">
          <button 
            type="button" 
            @click="closeModal" 
            class="btn-cancel" 
            :style="cancelBtnStyle"
          >
            <i class="fas fa-times"></i>
            Cancelar
          </button>
          <button 
            type="submit" 
            class="btn-submit" 
            :style="submitBtnStyle"
            :disabled="loading"
          >
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-save"></i>
            {{ loading ? 'Salvando...' : (task && task.id ? 'Atualizar' : 'Criar') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { userService } from '@/services/api'

export default {
  name: 'TaskModal',
  props: {
    isVisible: {
      type: Boolean,
      default: false
    },
    task: {
      type: Object,
      default: () => ({})
    },
    companies: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      form: {
        title: '',
        description: '',
        status: 'pendente',
        priority: 'media',
        user_id: '',
        due_date: ''
      },
      users: [],
      errors: {}
    }
  },
  computed: {
    minDate() {
      return new Date().toISOString().split('T')[0]
    },
    overlayStyle() {
      return {
        position: 'fixed',
        top: '0',
        left: '0',
        right: '0',
        bottom: '0',
        background: 'rgba(0, 0, 0, 0.5)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: 1000,
        padding: '20px'
      }
    },
    modalStyle() {
      return {
        background: 'white',
        borderRadius: '16px',
        width: '100%',
        maxWidth: '600px',
        maxHeight: '90vh',
        overflow: 'hidden',
        boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        animation: 'modalSlideIn 0.3s ease-out'
      }
    },
    headerStyle() {
      return {
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        padding: '24px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between'
      }
    },
    bodyStyle() {
      return {
        padding: '24px',
        maxHeight: '60vh',
        overflowY: 'auto'
      }
    },
    inputStyle() {
      return {
        width: '100%',
        padding: '12px 16px',
        border: '2px solid #e5e7eb',
        borderRadius: '8px',
        fontSize: '14px',
        transition: 'border-color 0.3s ease',
        fontFamily: 'inherit'
      }
    },
    textareaStyle() {
      return {
        ...this.inputStyle,
        minHeight: '80px',
        resize: 'vertical'
      }
    },
    selectStyle() {
      return {
        ...this.inputStyle,
        cursor: 'pointer'
      }
    },
    actionsStyle() {
      return {
        display: 'flex',
        gap: '12px',
        justifyContent: 'flex-end',
        marginTop: '24px',
        paddingTop: '20px',
        borderTop: '1px solid #f3f4f6'
      }
    },
    cancelBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 20px',
        background: '#f3f4f6',
        color: '#374151',
        border: 'none',
        borderRadius: '8px',
        cursor: 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        transition: 'all 0.3s ease'
      }
    },
    submitBtnStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        padding: '12px 20px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        border: 'none',
        borderRadius: '8px',
        cursor: this.loading ? 'not-allowed' : 'pointer',
        fontSize: '14px',
        fontWeight: '600',
        transition: 'all 0.3s ease',
        opacity: this.loading ? 0.7 : 1
      }
    },
    closeBtnStyle() {
      return {
        background: 'rgba(255, 255, 255, 0.2)',
        color: 'white',
        border: 'none',
        borderRadius: '6px',
        width: '36px',
        height: '36px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        cursor: 'pointer',
        transition: 'all 0.3s ease'
      }
    }
  },
  methods: {
    closeModal() {
      this.resetForm()
      this.$emit('close')
    },
    
    submitForm() {
      this.errors = {}
      
      // Validação básica
      if (!this.form.title.trim()) {
        this.errors.title = ['O título é obrigatório']
        return
      }
      
      if (!this.form.status) {
        this.errors.status = ['O status é obrigatório']
        return
      }
      
      if (!this.form.priority) {
        this.errors.priority = ['A prioridade é obrigatória']
        return
      }
      
      this.$emit('submit', { ...this.form })
    },
    
    resetForm() {
      this.form = {
        title: '',
        description: '',
        status: 'pendente',
        priority: 'media',
        user_id: '',
        due_date: ''
      }
      this.errors = {}
    },
    
    populateForm() {
      if (this.task && this.task.id) {
        this.form = {
          title: this.task.title || '',
          description: this.task.description || '',
          status: this.task.status || 'pendente',
          priority: this.task.priority || 'media',
          user_id: this.task.user_id || '',
          due_date: this.task.due_date || ''
        }
      } else {
        this.resetForm()
      }
    },
    
    async loadUsers() {
      try {
        const response = await userService.list()
        this.users = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar usuários:', error)
        this.users = []
      }
    },
    
    setErrors(errors) {
      this.errors = errors || {}
    }
  },
  
  watch: {
    isVisible(newValue) {
      if (newValue) {
        this.populateForm()
        this.loadUsers()
      } else {
        this.errors = {}
      }
    },
    
    task: {
      handler() {
        if (this.isVisible) {
          this.populateForm()
        }
      },
      deep: true
    }
  }
}
</script>

<style scoped>
@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

.form-group {
  margin-bottom: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
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

.form-label i {
  color: #667eea;
  width: 16px;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
  border-color: #ef4444;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #ef4444;
  font-size: 13px;
  margin-top: 6px;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.btn-cancel:hover {
  background: #e5e7eb;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .form-actions {
    flex-direction: column-reverse;
  }
  
  .btn-cancel,
  .btn-submit {
    width: 100%;
    justify-content: center;
  }
}
</style>
