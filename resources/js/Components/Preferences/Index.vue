<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-gray-900">Preferências</h1>
                    </div>
                </div>
            </header>

            <main class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <form @submit.prevent="savePreferences" class="space-y-8">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Dados Pessoais</h3>
                                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                                        <input 
                                            type="text" 
                                            id="name" 
                                            v-model="form.name"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            v-model="form.email"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="document" class="block text-sm font-medium text-gray-700">CPF/CNPJ</label>
                                        <input 
                                            type="text" 
                                            id="document" 
                                            v-model="form.document"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                        <input 
                                            type="tel" 
                                            id="phone" 
                                            v-model="form.phone"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Configurações do MEI</h3>
                                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="mei_limit" class="block text-sm font-medium text-gray-700">Limite Anual do MEI</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">R$</span>
                                            </div>
                                            <input 
                                                type="number" 
                                                id="mei_limit" 
                                                v-model="form.mei_limit"
                                                step="0.01"
                                                class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            />
                                        </div>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="mei_category" class="block text-sm font-medium text-gray-700">Categoria do MEI</label>
                                        <select 
                                            id="mei_category" 
                                            v-model="form.mei_category"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option value="comercio">Comércio</option>
                                            <option value="industria">Indústria</option>
                                            <option value="servicos">Serviços</option>
                                            <option value="transporte">Transporte</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Notificações</h3>
                                <div class="mt-6 space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                type="checkbox" 
                                                id="notify_invoice_due" 
                                                v-model="form.notify_invoice_due"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="notify_invoice_due" class="font-medium text-gray-700">Notificar sobre faturas próximas do vencimento</label>
                                            <p class="text-gray-500">Receba lembretes quando uma fatura estiver próxima do vencimento.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                type="checkbox" 
                                                id="notify_mei_limit" 
                                                v-model="form.notify_mei_limit"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="notify_mei_limit" class="font-medium text-gray-700">Notificar sobre limite do MEI</label>
                                            <p class="text-gray-500">Receba alertas quando estiver próximo de atingir o limite anual do MEI.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                type="checkbox" 
                                                id="notify_monthly_report" 
                                                v-model="form.notify_monthly_report"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="notify_monthly_report" class="font-medium text-gray-700">Receber relatório mensal</label>
                                            <p class="text-gray-500">Receba um relatório mensal com o resumo das suas finanças.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button 
                                type="button"
                                @click="changePassword"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Alterar Senha
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import Sidebar from '../Sidebar.vue';

export default {
    components: {
        Sidebar
    },
    setup() {
        const user = ref(null);
        const form = ref({
            name: '',
            email: '',
            document: '',
            phone: '',
            mei_limit: 81000,
            mei_category: 'servicos',
            notify_invoice_due: true,
            notify_mei_limit: true,
            notify_monthly_report: true
        });

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
                form.value = {
                    ...form.value,
                    ...data
                };
            } catch (error) {
                console.error('Erro ao carregar dados do usuário:', error);
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        };

        const savePreferences = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch('/api/preferences', {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(form.value)
                });

                if (!response.ok) {
                    throw new Error('Erro ao salvar preferências');
                }

                alert('Preferências salvas com sucesso!');
            } catch (error) {
                console.error('Erro ao salvar preferências:', error);
                alert('Erro ao salvar preferências. Tente novamente.');
            }
        };

        const changePassword = () => {
            alert('Funcionalidade em desenvolvimento');
        };

        onMounted(async () => {
            await loadUserData();
        });

        return {
            user,
            form,
            savePreferences,
            changePassword
        };
    }
};
</script> 