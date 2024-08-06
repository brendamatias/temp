<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Preferências</h1>

    <div class="mb-6">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-6">
      <div v-if="activeTab === 'companies'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-lg font-medium">Empresas Parceiras</h2>
          <button
            @click="openCompanyModal()"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
          >
            Nova Empresa
          </button>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <ul class="divide-y divide-gray-200">
            <li v-for="company in companies" :key="company.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-indigo-600 truncate">
                      {{ company.name }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                      CNPJ: {{ formatCNPJ(company.cnpj) }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                      Razão Social: {{ company.legal_name }}
                    </p>
                  </div>
                  <div class="ml-4 flex-shrink-0 flex space-x-2">
                    <button
                      @click="openCompanyModal(company)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Editar
                    </button>
                    <button
                      @click="deleteCompany(company.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Excluir
                    </button>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div v-if="activeTab === 'categories'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-lg font-medium">Categorias de Despesas</h2>
          <button
            @click="openCategoryModal()"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
          >
            Nova Categoria
          </button>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <ul class="divide-y divide-gray-200">
            <li v-for="category in categories" :key="category.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-indigo-600 truncate">
                      {{ category.name }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ category.description }}
                    </p>
                  </div>
                  <div class="ml-4 flex-shrink-0 flex space-x-2">
                    <button
                      @click="openCategoryModal(category)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Editar
                    </button>
                    <button
                      v-if="!category.is_archived"
                      @click="archiveCategory(category.id)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Arquivar
                    </button>
                    <button
                      v-else
                      @click="unarchiveCategory(category.id)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Desarquivar
                    </button>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div v-if="activeTab === 'settings'" class="space-y-6">
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Configurações do Sistema
            </h3>
            <div class="mt-6 space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Limite Anual MEI (R$)
                </label>
                <div class="mt-1">
                  <input
                    type="number"
                    v-model="settings.meiLimit"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  />
                </div>
              </div>

              <div class="space-y-4">
                <h4 class="text-sm font-medium text-gray-700">Alertas de Faturamento</h4>
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="settings.emailAlerts"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-900">
                    Receber alertas por e-mail
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="settings.smsAlerts"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-900">
                    Receber alertas por SMS
                  </label>
                </div>
              </div>

              <div class="mt-5">
                <button
                  @click="saveSettings"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Salvar Configurações
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCompanyModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingCompany ? 'Editar Empresa' : 'Nova Empresa' }}
            </h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input
                  type="text"
                  v-model="companyForm.cnpj"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input
                  type="text"
                  v-model="companyForm.name"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Razão Social</label>
                <input
                  type="text"
                  v-model="companyForm.legal_name"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveCompany"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Salvar
            </button>
            <button
              @click="showCompanyModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCategoryModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingCategory ? 'Editar Categoria' : 'Nova Categoria' }}
            </h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input
                  type="text"
                  v-model="categoryForm.name"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea
                  v-model="categoryForm.description"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                ></textarea>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveCategory"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Salvar
            </button>
            <button
              @click="showCategoryModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineComponent } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { themeService } from '../services/theme'

defineComponent({
  name: 'Preferences'
})

const tabs = [
  { id: 'companies', name: 'Empresas Parceiras' },
  { id: 'categories', name: 'Categorias' },
  { id: 'settings', name: 'Configurações' }
]

const activeTab = ref('companies')
const companies = ref([])
const categories = ref([])
const settings = ref({
  meiLimit: 81000,
  emailAlerts: false,
  smsAlerts: false
})

const showCompanyModal = ref(false)
const showCategoryModal = ref(false)
const editingCompany = ref(null)
const editingCategory = ref(null)

const companyForm = ref({
  cnpj: '',
  name: '',
  legal_name: ''
})

const categoryForm = ref({
  name: '',
  description: ''
})

const router = useRouter()

const loadData = async () => {
  console.log('Iniciando carregamento de dados...')
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    const [companiesRes, categoriesRes, settingsRes] = await Promise.all([
      axios.get('/api/partner-companies', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      }),
      axios.get('/api/expense-categories', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      }),
      axios.get('/api/preferences', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
    ])
    console.log('Dados carregados:', { companies: companiesRes.data, categories: categoriesRes.data, settings: settingsRes.data })
    companies.value = companiesRes.data
    categories.value = categoriesRes.data
    
    if (settingsRes.data) {
      settings.value = {
        meiLimit: settingsRes.data.mei_annual_limit || 81000,
        emailAlerts: settingsRes.data.email_notifications || false,
        smsAlerts: settingsRes.data.sms_notifications || false
      }
    }
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
  }
}

const openCompanyModal = (company = null) => {
  editingCompany.value = company
  if (company) {
    companyForm.value = { ...company }
  } else {
    companyForm.value = {
      cnpj: '',
      name: '',
      legal_name: ''
    }
  }
  showCompanyModal.value = true
}

const saveCompany = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    if (editingCompany.value) {
      await axios.put(`/api/partner-companies/${editingCompany.value.id}`, companyForm.value, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
    } else {
      await axios.post('/api/partner-companies', companyForm.value, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
    }
    showCompanyModal.value = false
    loadData()
  } catch (error) {
    console.error('Erro ao salvar empresa:', error)
  }
}

const deleteCompany = async (id) => {
  if (!confirm('Tem certeza que deseja excluir esta empresa?')) return
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    await axios.delete(`/api/partner-companies/${id}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    loadData()
  } catch (error) {
    console.error('Erro ao excluir empresa:', error)
  }
}

const openCategoryModal = (category = null) => {
  editingCategory.value = category
  if (category) {
    categoryForm.value = { ...category }
  } else {
    categoryForm.value = {
      name: '',
      description: ''
    }
  }
  showCategoryModal.value = true
}

const saveCategory = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    if (editingCategory.value) {
      await axios.put(`/api/expense-categories/${editingCategory.value.id}`, categoryForm.value, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
    } else {
      await axios.post('/api/expense-categories', categoryForm.value, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
    }
    showCategoryModal.value = false
    loadData()
  } catch (error) {
    console.error('Erro ao salvar categoria:', error)
  }
}

const archiveCategory = async (id) => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    await axios.post(`/api/expense-categories/${id}/deactivate`, {}, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    loadData()
  } catch (error) {
    console.error('Erro ao arquivar categoria:', error)
  }
}

const unarchiveCategory = async (id) => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    await axios.post(`/api/expense-categories/${id}/activate`, {}, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    loadData()
  } catch (error) {
    console.error('Erro ao desarquivar categoria:', error)
  }
}

const saveSettings = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    const settingsData = {
      theme: 'LIGHT',
      language: 'pt-BR',
      currency: 'BRL',
      date_format: 'd/m/Y',
      time_format: 'H:i',
      notifications_enabled: true,
      email_notifications: settings.value.emailAlerts,
      sms_notifications: settings.value.smsAlerts,
      mei_annual_limit: settings.value.meiLimit,
      mei_alert_threshold: 80,
      mei_monthly_alert_day: 15
    }

    console.log('Enviando dados:', settingsData)

    await axios.put('/api/preferences', settingsData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    alert('Configurações salvas com sucesso!')
    loadData()
  } catch (error) {
    console.error('Erro ao salvar configurações:', error)
    if (error.response?.data?.message) {
      const errorMessage = typeof error.response.data.message === 'string' 
        ? error.response.data.message 
        : JSON.stringify(error.response.data.message)
      alert(errorMessage)
    }
  }
}

const formatCNPJ = (cnpj) => {
  return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}

onMounted(() => {
  console.log('Componente Preferences montado')
  loadData()
})
</script> 