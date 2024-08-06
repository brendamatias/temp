<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Despesas
                        </h1>
                        <router-link 
                            to="/expenses/create"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nova Despesa
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
                                            placeholder="Buscar por descrição ou categoria..."
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <select 
                                            v-model="categoryFilter"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option value="">Todas as categorias</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <select 
                                            v-model="statusFilter"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option value="all">Todos os status</option>
                                            <option value="paid">Pago</option>
                                            <option value="pending">Pendente</option>
                                        </select>
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <input 
                                            type="month" 
                                            v-model="monthFilter"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Descrição
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Categoria
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Valor
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data
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
                                        <tr v-if="!expenses || expenses.length === 0">
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Carregando despesas...
                                            </td>
                                        </tr>
                                        <tr v-else-if="filteredExpenses.length === 0">
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Nenhuma despesa encontrada
                                            </td>
                                        </tr>
                                        <tr v-else v-for="expense in filteredExpenses" :key="expense.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ expense.description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ getCategoryName(expense.category_id) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                R$ {{ formatValue(expense.value) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(expense.date) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span 
                                                    :class="[
                                                        expense.is_paid 
                                                            ? 'bg-green-100 text-green-800' 
                                                            : 'bg-yellow-100 text-yellow-800',
                                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                                    ]"
                                                >
                                                    {{ expense.is_paid ? 'Pago' : 'Pendente' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <router-link 
                                                        :to="`/expenses/${expense.id}/edit`"
                                                        class="text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </router-link>
                                                    <button 
                                                        v-if="!expense.is_paid"
                                                        @click="markAsPaid(expense)"
                                                        class="text-green-600 hover:text-green-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        v-else
                                                        @click="markAsPending(expense)"
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        @click="deleteExpense(expense)"
                                                        class="text-red-600 hover:text-red-900"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
    name: 'ExpensesIndex',
    components: {
        Sidebar
    },
    setup() {
        const toast = useToast();
        const user = ref(null);
        const expenses = ref([]);
        const categories = ref([]);
        const search = ref('');
        const categoryFilter = ref('');
        const statusFilter = ref('all');
        const monthFilter = ref('');

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
                console.log('Categorias carregadas:', categories.value);
            } catch (error) {
                console.error('Erro ao carregar categorias:', error);
                toast.error(error.message || 'Erro ao carregar categorias');
            }
        };

        const loadExpenses = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch('/api/expenses', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar despesas');
                }

                const data = await response.json();
                expenses.value = Array.isArray(data) ? data : data.data;
                console.log('Despesas carregadas:', expenses.value);
            } catch (error) {
                console.error('Erro ao carregar despesas:', error);
                toast.error(error.message || 'Erro ao carregar despesas');
            }
        };

        const markAsPaid = async (expense) => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/expenses/${expense.id}/mark-as-paid`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao marcar despesa como paga');
                }

                toast.success('Despesa marcada como paga com sucesso!');
                await loadExpenses();
            } catch (error) {
                console.error('Erro ao marcar despesa como paga:', error);
                toast.error(error.message || 'Erro ao marcar despesa como paga');
            }
        };

        const markAsPending = async (expense) => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/expenses/${expense.id}/mark-as-pending`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao marcar despesa como pendente');
                }

                toast.success('Despesa marcada como pendente com sucesso!');
                await loadExpenses();
            } catch (error) {
                console.error('Erro ao marcar despesa como pendente:', error);
                toast.error(error.message || 'Erro ao marcar despesa como pendente');
            }
        };

        const deleteExpense = async (expense) => {
            if (!confirm('Tem certeza que deseja excluir esta despesa?')) {
                return;
            }

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/expenses/${expense.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao excluir despesa');
                }

                toast.success('Despesa excluída com sucesso!');
                await loadExpenses();
            } catch (error) {
                console.error('Erro ao excluir despesa:', error);
                toast.error(error.message || 'Erro ao excluir despesa');
            }
        };

        const getCategoryName = (categoryId) => {
            const category = categories.value.find(c => c.id === categoryId);
            return category ? category.name : 'Categoria não encontrada';
        };

        const formatValue = (value) => {
            return value.toFixed(2).replace('.', ',');
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('pt-BR');
        };

        const filteredExpenses = computed(() => {
            if (!expenses.value) return [];
            
            let result = expenses.value;

            if (search.value) {
                const searchLower = search.value.toLowerCase();
                result = result.filter(expense => 
                    expense.description.toLowerCase().includes(searchLower) ||
                    getCategoryName(expense.category_id).toLowerCase().includes(searchLower)
                );
            }

            if (categoryFilter.value) {
                result = result.filter(expense => expense.category_id === parseInt(categoryFilter.value));
            }

            if (statusFilter.value !== 'all') {
                const isPaid = statusFilter.value === 'paid';
                result = result.filter(expense => expense.is_paid === isPaid);
            }

            if (monthFilter.value) {
                const [year, month] = monthFilter.value.split('-');
                result = result.filter(expense => {
                    const expenseDate = new Date(expense.date);
                    return expenseDate.getFullYear() === parseInt(year) && 
                           expenseDate.getMonth() === parseInt(month) - 1;
                });
            }

            return result;
        });

        onMounted(async () => {
            await loadUserData();
            await loadCategories();
            await loadExpenses();
        });

        return {
            user,
            expenses,
            categories,
            search,
            categoryFilter,
            statusFilter,
            monthFilter,
            filteredExpenses,
            getCategoryName,
            formatValue,
            formatDate,
            markAsPaid,
            markAsPending,
            deleteExpense
        };
    }
};
</script> 