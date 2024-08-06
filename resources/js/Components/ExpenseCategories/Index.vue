<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Categorias de Despesas
                        </h1>
                        <router-link 
                            to="/expense-categories/create"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nova Categoria
                        </router-link>
                    </div>
                </div>
            </header>

            <main class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="mb-6">
                                <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
                                    <div class="flex-1">
                                        <input 
                                            type="text" 
                                            v-model="search"
                                            placeholder="Buscar categoria..."
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <select 
                                            v-model="statusFilter"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option value="all">Todos</option>
                                            <option value="active">Ativos</option>
                                            <option value="inactive">Inativos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nome
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Descrição
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Ações</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-if="!categories || categories.length === 0">
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Carregando categorias...
                                            </td>
                                        </tr>
                                        <tr v-else-if="filteredCategories.length === 0">
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Nenhuma categoria encontrada
                                            </td>
                                        </tr>
                                        <tr v-else v-for="category in filteredCategories" :key="category.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ category.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ category.description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span 
                                                    :class="[
                                                        category.is_active 
                                                            ? 'bg-green-100 text-green-800' 
                                                            : 'bg-red-100 text-red-800',
                                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                                    ]"
                                                >
                                                    {{ category.is_active ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <router-link 
                                                        :to="`/expense-categories/${category.id}/edit`"
                                                        class="text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </router-link>
                                                    <button 
                                                        v-if="category.is_active"
                                                        @click="deactivateCategory(category)"
                                                        class="text-red-600 hover:text-red-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        v-else
                                                        @click="activateCategory(category)"
                                                        class="text-green-600 hover:text-green-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import Sidebar from '../Sidebar.vue';

export default {
    name: 'ExpenseCategoriesIndex',
    components: {
        Sidebar
    },
    setup() {
        const toast = useToast();
        const user = ref(null);
        const categories = ref([]);
        const search = ref('');
        const statusFilter = ref('all');

        const loadUserData = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar dados do usuário');
                }

                const data = await response.json();
                user.value = data;
            } catch (error) {
                console.error('Erro ao carregar dados do usuário:', error);
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        };

        const loadCategories = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch('/api/expense-categories', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar categorias');
                }

                const data = await response.json();
                categories.value = Array.isArray(data) ? data : data.data;
            } catch (error) {
                console.error('Erro ao carregar categorias:', error);
                toast.error(error.message || 'Erro ao carregar categorias');
            }
        };

        const activateCategory = async (category) => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/expense-categories/${category.id}/activate`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao ativar categoria');
                }

                toast.success('Categoria ativada com sucesso!');
                await loadCategories();
            } catch (error) {
                console.error('Erro ao ativar categoria:', error);
                toast.error(error.message || 'Erro ao ativar categoria');
            }
        };

        const deactivateCategory = async (category) => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/expense-categories/${category.id}/deactivate`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao desativar categoria');
                }

                toast.success('Categoria desativada com sucesso!');
                await loadCategories();
            } catch (error) {
                console.error('Erro ao desativar categoria:', error);
                toast.error(error.message || 'Erro ao desativar categoria');
            }
        };

        const filteredCategories = computed(() => {
            if (!categories.value) return [];
            
            let result = categories.value;

            if (search.value) {
                const searchLower = search.value.toLowerCase();
                result = result.filter(category => 
                    category.name.toLowerCase().includes(searchLower) ||
                    (category.description && category.description.toLowerCase().includes(searchLower))
                );
            }

            if (statusFilter.value !== 'all') {
                const isActive = statusFilter.value === 'active';
                result = result.filter(category => category.is_active === isActive);
            }

            return result;
        });

        onMounted(async () => {
            await loadUserData();
            await loadCategories();
        });

        return {
            user,
            categories,
            search,
            statusFilter,
            filteredCategories,
            activateCategory,
            deactivateCategory
        };
    }
};
</script> 