<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">Notas Fiscais</h1>
                        <router-link 
                            to="/invoices/create"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                                Lançar Nota Fiscal
                        </router-link>
                    </div>
                </div>
            </header>

            <main class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="mb-6 bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input 
                                            type="text" 
                                            id="search"
                                            v-model="search"
                                            placeholder="Número, empresa ou descrição..."
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700">Período</label>
                                    <div class="mt-1 grid grid-cols-2 gap-2">
                                        <input 
                                            type="month" 
                                            id="start_date"
                                            v-model="startDate"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                        <input 
                                            type="month" 
                                            id="end_date"
                                            v-model="endDate"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <div class="mt-1">
                                        <select 
                                            id="status"
                                            v-model="status"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option value="">Todos</option>
                                            <option value="active">Ativas</option>
                                            <option value="inactive">Inativas</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-end">
                                    <button 
                                        @click="loadInvoices"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Número
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Empresa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Valor
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Competência
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Recebimento
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
                                    <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ invoice.number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ invoice.partner_company_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatValue(invoice.value) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatMonth(invoice.competence_month) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(invoice.receipt_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                :class="[
                                                    invoice.is_active 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-red-100 text-red-800',
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                                ]"
                                            >
                                                {{ invoice.is_active ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <router-link 
                                                    :to="`/invoices/${invoice.id}/edit`"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </router-link>
                                                <button 
                                                    @click="deleteInvoice(invoice)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredInvoices.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Nenhuma nota fiscal encontrada
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    name: 'InvoicesIndex',
    components: {
        Sidebar
    },
    setup() {
        const toast = useToast();
        const user = ref(null);
        const invoices = ref([]);
        const search = ref('');
        const startDate = ref('');
        const endDate = ref('');
        const status = ref('');

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

        const loadInvoices = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                let url = '/api/invoices?';
                const params = new URLSearchParams();

                if (search.value) {
                    params.append('search', search.value);
                }

                if (startDate.value) {
                    params.append('start_date', startDate.value);
                }

                if (endDate.value) {
                    params.append('end_date', endDate.value);
                }

                if (status.value) {
                    params.append('status', status.value);
                }

                url += params.toString();

                const response = await fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar notas fiscais');
                }

                const data = await response.json();
                invoices.value = data;
            } catch (error) {
                console.error('Erro ao carregar notas fiscais:', error);
                toast.error(error.message || 'Erro ao carregar notas fiscais');
            }
        };

        const deleteInvoice = async (invoice) => {
            if (!confirm('Tem certeza que deseja excluir esta nota fiscal?')) {
                return;
            }

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/invoices/${invoice.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao excluir nota fiscal');
                }

                await loadInvoices();
                toast.success('Nota fiscal excluída com sucesso!');
            } catch (error) {
                console.error('Erro ao excluir nota fiscal:', error);
                toast.error(error.message || 'Erro ao excluir nota fiscal');
            }
        };

        const filteredInvoices = computed(() => {
            return invoices.value.filter(invoice => {
                const matchesSearch = !search.value || 
                    invoice.number.toLowerCase().includes(search.value.toLowerCase()) ||
                    invoice.partner_company_name.toLowerCase().includes(search.value.toLowerCase()) ||
                    invoice.service_description.toLowerCase().includes(search.value.toLowerCase());

                const matchesDate = (!startDate.value || invoice.competence_month >= startDate.value) &&
                    (!endDate.value || invoice.competence_month <= endDate.value);

                const matchesStatus = !status.value || 
                    (status.value === 'active' && invoice.is_active) ||
                    (status.value === 'inactive' && !invoice.is_active);

                return matchesSearch && matchesDate && matchesStatus;
            });
        });

        const formatValue = (value) => {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        };

        const formatDate = (date) => {
            if (!date) return '';
            return new Date(date).toLocaleDateString('pt-BR');
        };

        const formatMonth = (month) => {
            if (!month) return '';
            const [year, monthNumber] = month.split('-');
            return new Date(year, monthNumber - 1).toLocaleDateString('pt-BR', {
                month: 'long',
                year: 'numeric'
            });
        };

        onMounted(async () => {
            await loadUserData();
            await loadInvoices();
        });

        return {
            user,
            invoices,
            search,
            startDate,
            endDate,
            status,
            filteredInvoices,
            loadInvoices,
            deleteInvoice,
            formatValue,
            formatDate,
            formatMonth
        };
    }
};
</script> 