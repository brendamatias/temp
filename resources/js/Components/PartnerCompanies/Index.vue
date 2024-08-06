<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">Empresas Parceiras</h1>
                        <router-link 
                            to="/partner-companies/create"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nova Empresa
                        </router-link>
                    </div>
                </div>
            </header>

            <main class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="mb-6 bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input 
                                            type="text" 
                                            id="search"
                                            v-model="search"
                                            placeholder="Nome ou CNPJ da empresa..."
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
                                    <label for="include_inactive" class="block text-sm font-medium text-gray-700">Status</label>
                                    <div class="mt-1">
                                        <select 
                                            id="include_inactive"
                                            v-model="includeInactive"
                                            class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option :value="false">Ativas</option>
                                            <option :value="true">Todas</option>
                                        </select>
                                    </div>
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
                                            Nome
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Razão Social
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            CNPJ
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
                                    <tr v-for="company in filteredCompanies" :key="company.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ company.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ company.legal_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDocument(company.document) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                :class="[
                                                    company.is_active 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-red-100 text-red-800',
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                                ]"
                                            >
                                                {{ company.is_active ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <router-link 
                                                    :to="`/partner-companies/${company.id}/edit`"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </router-link>
                                                <button 
                                                    @click="deleteCompany(company)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredCompanies.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Nenhuma empresa encontrada
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
    name: 'PartnerCompaniesIndex',
    components: {
        Sidebar
    },
    setup() {
        const toast = useToast();
        const user = ref(null);
        const companies = ref([]);
        const search = ref('');
        const includeInactive = ref(false);

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

        const loadCompanies = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                console.log('Carregando empresas...');
                const response = await fetch('/api/partner-companies', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar empresas');
                }

                const { data } = await response.json();
                console.log('Empresas carregadas:', data);
                companies.value = data;
            } catch (error) {
                console.error('Erro ao carregar empresas:', error);
                toast.error(error.message || 'Erro ao carregar empresas');
            }
        };

        const deleteCompany = async (company) => {
            if (!confirm('Tem certeza que deseja excluir esta empresa?')) {
                return;
            }

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/partner-companies/${company.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao excluir empresa');
                }

                await loadCompanies();
                toast.success('Empresa excluída com sucesso!');
            } catch (error) {
                console.error('Erro ao excluir empresa:', error);
                toast.error(error.message || 'Erro ao excluir empresa');
            }
        };

        const filteredCompanies = computed(() => {
            console.log('Filtrando empresas:', companies.value);
            return companies.value.filter(company => {
                const matchesSearch = !search.value || 
                    company.name.toLowerCase().includes(search.value.toLowerCase()) ||
                    company.document.toLowerCase().includes(search.value.toLowerCase());

                const matchesStatus = includeInactive.value || company.is_active;

                return matchesSearch && matchesStatus;
            });
        });

        const formatDocument = (document) => {
            if (!document) return '';
            return document.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
        };

        const formatPhone = (phone) => {
            if (!phone) return '';
            return phone.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
        };

        onMounted(async () => {
            await loadUserData();
            await loadCompanies();
        });

        return {
            user,
            companies,
            search,
            includeInactive,
            filteredCompanies,
            loadCompanies,
            deleteCompany,
            formatDocument,
            formatPhone
        };
    }
};
</script> 