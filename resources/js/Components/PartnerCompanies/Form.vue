<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ isEditing ? 'Editar Empresa Parceira' : 'Nova Empresa Parceira' }}
                        </h1>
                        <router-link 
                            to="/partner-companies"
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
                            <form @submit.prevent="saveCompany" class="space-y-8 divide-y divide-gray-200">
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Dados Básicos</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Informações principais da empresa parceira.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="name" class="block text-sm font-medium text-gray-700">
                                                Nome Fantasia
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="name"
                                                    v-model="form.name"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.name }"
                                                />
                                                <p v-if="errors.name" class="mt-2 text-sm text-red-600">
                                                    {{ errors.name[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="legal_name" class="block text-sm font-medium text-gray-700">
                                                Razão Social
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="legal_name"
                                                    v-model="form.legal_name"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.legal_name }"
                                                />
                                                <p v-if="errors.legal_name" class="mt-2 text-sm text-red-600">
                                                    {{ errors.legal_name[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="document" class="block text-sm font-medium text-gray-700">
                                                CNPJ
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="document"
                                                    v-model="form.document"
                                                    v-mask="'##.###.###/####-##'"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.document }"
                                                />
                                                <p v-if="errors.document" class="mt-2 text-sm text-red-600">
                                                    {{ errors.document[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="state_registration" class="block text-sm font-medium text-gray-700">
                                                Inscrição Estadual
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="state_registration"
                                                    v-model="form.state_registration"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.state_registration }"
                                                />
                                                <p v-if="errors.state_registration" class="mt-2 text-sm text-red-600">
                                                    {{ errors.state_registration[0] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8 space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Contato</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Informações de contato da empresa.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="email" class="block text-sm font-medium text-gray-700">
                                                Email
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="email" 
                                                    id="email"
                                                    v-model="form.email"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.email }"
                                                />
                                                <p v-if="errors.email" class="mt-2 text-sm text-red-600">
                                                    {{ errors.email[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                                Telefone
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="phone"
                                                    v-model="form.phone"
                                                    v-mask="'(##) #####-####'"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.phone }"
                                                />
                                                <p v-if="errors.phone" class="mt-2 text-sm text-red-600">
                                                    {{ errors.phone[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-6">
                                            <label for="address" class="block text-sm font-medium text-gray-700">
                                                Endereço
                                            </label>
                                            <div class="mt-1">
                                                <input 
                                                    type="text" 
                                                    id="address"
                                                    v-model="form.address"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.address }"
                                                />
                                                <p v-if="errors.address" class="mt-2 text-sm text-red-600">
                                                    {{ errors.address[0] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8 space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Status</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Defina o status da empresa parceira.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <div class="flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    id="is_active"
                                                    v-model="form.is_active"
                                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                />
                                                <label for="is_active" class="block ml-2 text-sm text-gray-900">
                                                    Empresa Ativa
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8">
                                    <div class="flex justify-end space-x-3">
                                        <router-link 
                                            to="/partner-companies"
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
                                            {{ isEditing ? 'Salvar Alterações' : 'Criar Empresa' }}
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
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import Sidebar from '../Sidebar.vue';

export default {
    name: 'PartnerCompanyForm',
    components: {
        Sidebar
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const toast = useToast();
        const user = ref(null);
        const loading = ref(false);
        const errors = ref({});

        const form = ref({
            name: '',
            legal_name: '',
            document: '',
            state_registration: '',
            email: '',
            phone: '',
            address: '',
            is_active: true
        });

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

        const loadCompany = async () => {
            if (!isEditing.value) return;

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/partner-companies/${route.params.id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar empresa');
                }

                const data = await response.json();
                form.value = {
                    name: data.name,
                    legal_name: data.legal_name,
                    document: data.document,
                    state_registration: data.state_registration,
                    email: data.email,
                    phone: data.phone,
                    address: data.address,
                    is_active: data.is_active
                };
            } catch (error) {
                console.error('Erro ao carregar empresa:', error);
                toast.error(error.message || 'Erro ao carregar empresa');
                router.push('/partner-companies');
            }
        };

        const saveCompany = async () => {
            loading.value = true;
            errors.value = {};

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const url = isEditing.value 
                    ? `/api/partner-companies/${route.params.id}`
                    : '/api/partner-companies';

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
                    throw new Error(data.message || 'Erro ao salvar empresa');
                }

                toast.success(isEditing.value 
                    ? 'Empresa atualizada com sucesso!' 
                    : 'Empresa criada com sucesso!'
                );
                router.push('/partner-companies');
            } catch (error) {
                console.error('Erro ao salvar empresa:', error);
                toast.error(error.message);
            } finally {
                loading.value = false;
            }
        };

        onMounted(async () => {
            await loadUserData();
            await loadCompany();
        });

        return {
            user,
            form,
            errors,
            loading,
            isEditing,
            saveCompany
        };
    }
};
</script> 