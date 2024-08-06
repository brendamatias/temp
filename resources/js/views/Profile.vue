<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Meu Perfil</h1>

      <form @submit.prevent="updateProfile" class="space-y-6">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            class="mt-1 block w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="mt-1 block w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          />
        </div>

        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
          <input
            type="tel"
            id="phone"
            v-model="form.phone"
            class="mt-1 block w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="(00) 00000-0000"
          />
        </div>

        <div>
          <label for="current_password" class="block text-sm font-medium text-gray-700">Senha Atual</label>
          <input
            type="password"
            id="current_password"
            v-model="form.current_password"
            class="mt-1 block w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            class="mt-1 block w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="form.password_confirmation"
            class="mt-1 block w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="resetForm"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="loading"
          >
            {{ loading ? 'Salvando...' : 'Salvar Alterações' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'

export default {
  name: 'Profile',
  setup() {
    const toast = useToast()
    const loading = ref(false)
    const form = ref({
      name: '',
      email: '',
      phone: '',
      current_password: '',
      password: '',
      password_confirmation: ''
    })

    const loadUserData = async () => {
      try {
        const response = await window.axios.get('/api/user')
        const user = response.data
        form.value.name = user.name
        form.value.email = user.email
        form.value.phone = user.phone || ''
      } catch (error) {
        toast.error('Erro ao carregar dados do usuário')
      }
    }

    const updateProfile = async () => {
      loading.value = true
      try {
        await window.axios.put('/api/user/profile', form.value)
        toast.success('Perfil atualizado com sucesso!')
        resetForm()
      } catch (error) {
        if (error.response?.data?.errors) {
          Object.values(error.response.data.errors).forEach(messages => {
            messages.forEach(message => toast.error(message))
          })
        } else {
          toast.error('Erro ao atualizar perfil')
        }
      } finally {
        loading.value = false
      }
    }

    const resetForm = () => {
      form.value.current_password = ''
      form.value.password = ''
      form.value.password_confirmation = ''
    }

    onMounted(() => {
      loadUserData()
    })

    return {
      form,
      loading,
      updateProfile,
      resetForm
    }
  }
}
</script> 