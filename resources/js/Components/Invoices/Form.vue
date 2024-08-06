<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ isEditing ? 'Editar Nota Fiscal' : 'Lançar Nota Fiscal' }}
                        </h1>
                        <router-link 
                            to="/invoices"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </router-link>
                    </div>
                </div>
            </header>

            <main class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <form @submit.prevent="saveInvoice" class="space-y-8 divide-y divide-gray-200">
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Empresa Parceira</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Selecione a empresa parceira para a nota fiscal.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-6">
                                            <label for="company_search" class="block text-sm font-medium text-gray-700">
                                                Buscar Empresa
                                            </label>
                                            <div class="mt-1 relative">
                                                <input 
                                                    type="text" 
                                                    id="company_search"
                                                    v-model="companySearch"
                                                    placeholder="Digite o CNPJ ou nome da empresa..."
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.partner_company_id }"
                                                />
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p v-if="errors.partner_company_id" class="mt-2 text-sm text-red-600">
                                                {{ errors.partner_company_id[0] }}
                                            </p>

                                            <div v-if="searchResults.length > 0 && !selectedCompany" class="mt-2 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-sm">
                                                <ul class="divide-y divide-gray-200">
                                                    <li v-for="company in searchResults" :key="company.id" class="p-4 hover:bg-gray-50 cursor-pointer" @click="selectCompany(company)">
                                                        <div class="flex justify-between">
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900">{{ company.name }}</p>
                                                                <p class="text-sm text-gray-500">{{ formatDocument(company.document) }}</p>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div v-if="selectedCompany" class="mt-2 p-4 bg-gray-50 border border-gray-300 rounded-md">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ selectedCompany.name }}</p>
                                                        <p class="text-sm text-gray-500">{{ formatDocument(selectedCompany.document) }}</p>
                                                    </div>
                                                    <button 
                                                        type="button"
                                                        @click="selectedCompany = null"
                                                        class="text-sm text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        Alterar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8 space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Dados da Nota Fiscal</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Informações da nota fiscal.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="number" class="block text-sm font-medium text-gray-700">
                                                Número
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="number"
                                                    v-model="form.number"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.number }"
                                                />
                                                <p v-if="errors.number" class="mt-2 text-sm text-red-600">
                                                    {{ errors.number[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="value" class="block text-sm font-medium text-gray-700">
                                                Valor
                                            </label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">R$</span>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    id="value"
                                                    v-model="form.value"
                                                    v-money="money"
                                                    class="block w-full pl-7 pr-12 px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.value }"
                                                />
                                                <p v-if="errors.value" class="mt-2 text-sm text-red-600">
                                                    {{ errors.value[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="competence_month" class="block text-sm font-medium text-gray-700">
                                                Mês de Competência
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="month" 
                                                    id="competence_month"
                                                    v-model="form.competence_month"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.competence_month }"
                                                />
                                                <p v-if="errors.competence_month" class="mt-2 text-sm text-red-600">
                                                    {{ errors.competence_month[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="receipt_date" class="block text-sm font-medium text-gray-700">
                                                Data de Recebimento
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="date" 
                                                    id="receipt_date"
                                                    v-model="form.receipt_date"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.receipt_date }"
                                                />
                                                <p v-if="errors.receipt_date" class="mt-2 text-sm text-red-600">
                                                    {{ errors.receipt_date[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-6">
                                            <label for="service_description" class="block text-sm font-medium text-gray-700">
                                                Descrição do Serviço
                                            </label>
                                            <div class="mt-1">
                                                <textarea 
                                                    id="service_description"
                                                    v-model="form.service_description"
                                                    rows="3"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.service_description }"
                                                ></textarea>
                                                <p v-if="errors.service_description" class="mt-2 text-sm text-red-600">
                                                    {{ errors.service_description[0] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8">
                                    <div class="flex justify-end space-x-3">
                                        <router-link 
                                            to="/invoices"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Cancelar
                                        </router-link>
                                        <button 
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            :disabled="loading"
                                        >
                                            <svg v-if="loading" class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            {{ isEditing ? 'Salvar Alterações' : 'Lançar Nota Fiscal' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { VMoney } from 'v-money3';
import Sidebar from '../Sidebar.vue';

export default {
    name: 'InvoiceForm',
    components: {
        Sidebar
    },
    directives: {
        money: VMoney
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const toast = useToast();
        const user = ref(null);
        const loading = ref(false);
        const errors = ref({});
        const companySearch = ref('');
        const searchResults = ref([]);
        const selectedCompany = ref(null);

        const form = ref({
            number: '',
            value: '',
            service_description: '',
            competence_month: '',
            receipt_date: '',
            partner_company_id: null
        });

        const money = {
            decimal: ',',
            thousands: '.',
            prefix: 'R$ ',
            precision: 2,
            masked: false
        };

        const isEditing = computed(() => route.params.id !== undefined);

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

        const loadInvoice = async () => {
            if (!isEditing.value) return;

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/invoices/${route.params.id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar nota fiscal');
                }

                const data = await response.json();
                form.value = {
                    number: data.number,
                    value: data.value,
                    service_description: data.service_description,
                    competence_month: data.competence_month,
                    receipt_date: data.receipt_date,
                    partner_company_id: data.partner_company_id
                };

                if (data.partner_company) {
                    selectedCompany.value = data.partner_company;
                }
            } catch (error) {
                console.error('Erro ao carregar nota fiscal:', error);
                toast.error(error.message || 'Erro ao carregar nota fiscal');
                router.push('/invoices');
            }
        };

        const searchCompanies = async () => {
            if (!companySearch.value) {
                searchResults.value = [];
                return;
            }

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/partner-companies/search?q=${encodeURIComponent(companySearch.value)}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao buscar empresas');
                }

                const data = await response.json();
                searchResults.value = data;
            } catch (error) {
                console.error('Erro ao buscar empresas:', error);
                toast.error(error.message || 'Erro ao buscar empresas');
            }
        };

        const selectCompany = (company) => {
            selectedCompany.value = company;
            form.value.partner_company_id = company.id;
            companySearch.value = '';
            searchResults.value = [];
        };

        const formatDocument = (document) => {
            if (!document) return '';
            return document.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        };

        const saveInvoice = async () => {
            loading.value = true;
            errors.value = {};

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const url = isEditing.value 
                    ? `/api/invoices/${route.params.id}`
                    : '/api/invoices';

                const response = await fetch(url, {
                    method: isEditing.value ? 'PUT' : 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(form.value)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        errors.value = data.errors;
                        throw new Error('Por favor, corrija os erros no formulário');
                    }
                    throw new Error(data.message || 'Erro ao salvar nota fiscal');
                }

                toast.success(isEditing.value 
                    ? 'Nota fiscal atualizada com sucesso!' 
                    : 'Nota fiscal lançada com sucesso!'
                );
                router.push('/invoices');
            } catch (error) {
                console.error('Erro ao salvar nota fiscal:', error);
                toast.error(error.message);
            } finally {
                loading.value = false;
            }
        };

        watch(companySearch, () => {
            searchCompanies();
        });

        onMounted(async () => {
            await loadUserData();
            await loadInvoice();
        });

        return {
            user,
            form,
            errors,
            loading,
            isEditing,
            companySearch,
            searchResults,
            selectedCompany,
            money,
            saveInvoice,
            selectCompany,
            formatDocument
        };
    }
};
</script> 