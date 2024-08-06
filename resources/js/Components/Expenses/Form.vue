<template>
    <div class="min-h-screen bg-gray-100">
        <Sidebar :user="user" />
        
        <div class="lg:pl-64">
            <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
                <div class="flex flex-1 justify-between px-4">
                    <div class="flex flex-1">
                        <h1 class="text-2xl font-semibold text-gray-900 my-auto">
                            {{ isEditing ? 'Editar Despesa' : 'Nova Despesa' }}
                        </h1>
                    </div>
                </div>
            </div>

            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                        <form @submit.prevent="saveExpense" class="px-4 py-6 sm:p-8">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4">
                                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nome</label>
                                    <div class="mt-2">
                                        <input 
                                            type="text" 
                                            id="name" 
                                            v-model="form.name"
                                            class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            :class="{ 'ring-red-300': errors.name }"
                                        />
                                        <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name }}</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">Categoria</label>
                                    <div class="mt-2">
                                        <select 
                                            id="category_id" 
                                            v-model="form.category_id"
                                            class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            :class="{ 'ring-red-300': errors.category_id }"
                                        >
                                            <option value="">Selecione uma categoria</option>
                                            <option 
                                                v-for="category in categories" 
                                                :key="category.id" 
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <p v-if="errors.category_id" class="mt-2 text-sm text-red-600">{{ errors.category_id }}</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="payment_date" class="block text-sm font-medium leading-6 text-gray-900">Data de Pagamento</label>
                                    <div class="mt-2">
                                        <input 
                                            type="date" 
                                            id="payment_date" 
                                            v-model="form.payment_date"
                                            class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            :class="{ 'ring-red-300': errors.payment_date }"
                                        />
                                        <p v-if="errors.payment_date" class="mt-2 text-sm text-red-600">{{ errors.payment_date }}</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="competence_date" class="block text-sm font-medium leading-6 text-gray-900">Data de Competência</label>
                                    <div class="mt-2">
                                        <input 
                                            type="date" 
                                            id="competence_date" 
                                            v-model="form.competence_date"
                                            class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            :class="{ 'ring-red-300': errors.competence_date }"
                                        />
                                        <p v-if="errors.competence_date" class="mt-2 text-sm text-red-600">{{ errors.competence_date }}</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="value" class="block text-sm font-medium leading-6 text-gray-900">Valor</label>
                                    <div class="mt-2">
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">R$</span>
                                            </div>
                                            <input 
                                                type="number" 
                                                id="value" 
                                                v-model="form.value"
                                                step="0.01"
                                                class="block w-full rounded-md border-0 py-3 pl-10 pr-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                :class="{ 'ring-red-300': errors.value }"
                                            />
                                        </div>
                                        <p v-if="errors.value" class="mt-2 text-sm text-red-600">{{ errors.value }}</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <div class="mt-8">
                                        <div class="relative flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input 
                                                    type="checkbox" 
                                                    id="is_paid" 
                                                    v-model="form.is_paid"
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                />
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="is_paid" class="font-medium text-gray-900">Despesa Paga</label>
                                                <p class="text-gray-500">Marque se a despesa já foi paga</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-end gap-x-6">
                                <router-link 
                                    to="/expenses"
                                    class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700"
                                >
                                    Cancelar
                                </router-link>
                                <button 
                                    type="submit"
                                    class="rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    :disabled="loading"
                                >
                                    <span v-if="loading" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Salvando...
                                    </span>
                                    <span v-else>{{ isEditing ? 'Salvar Alterações' : 'Criar Despesa' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import Sidebar from '../Sidebar.vue';

export default {
    components: {
        Sidebar
    },
    setup() {
        const toast = useToast();
        const route = useRoute();
        const router = useRouter();
        const user = ref(null);
        const loading = ref(false);
        const isEditing = computed(() => route.params.id !== undefined);
        const categories = ref([]);

        const form = ref({
            name: '',
            category_id: '',
            payment_date: '',
            competence_date: '',
            value: '',
            is_paid: false
        });

        const errors = ref({
            name: '',
            category_id: '',
            payment_date: '',
            competence_date: '',
            value: '',
            is_paid: ''
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
            } catch (error) {
                console.error('Erro ao carregar dados do usuário:', error);
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        };

        const loadExpense = async () => {
            try {
                loading.value = true;
                const token = localStorage.getItem('token');
                if (!token) {
                    router.push('/login');
                    return;
                }

                const response = await fetch(`/api/expenses/${route.params.id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar despesa');
                }

                const data = await response.json();
                form.value = {
                    name: data.name,
                    category_id: data.category_id,
                    payment_date: data.payment_date,
                    competence_date: data.competence_date,
                    value: data.value,
                    is_paid: data.is_paid
                };
            } catch (error) {
                console.error('Erro ao carregar despesa:', error);
                toast.error('Erro ao carregar despesa');
                router.push('/expenses');
            } finally {
                loading.value = false;
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

        const saveExpense = async () => {
            try {
                loading.value = true;
                const token = localStorage.getItem('token');
                if (!token) {
                    router.push('/login');
                    return;
                }

                const url = isEditing.value 
                    ? `/api/expenses/${route.params.id}`
                    : '/api/expenses';

                const response = await fetch(url, {
                    method: isEditing.value ? 'PUT' : 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ...form.value,
                        is_active: form.value.is_active
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (errorData.errors) {
                        errors.value = errorData.errors;
                        throw new Error('Por favor, corrija os erros no formulário');
                    }
                    throw new Error(errorData.message || 'Erro ao salvar despesa');
                }

                toast.success(isEditing.value ? 'Despesa atualizada com sucesso!' : 'Despesa criada com sucesso!');
                router.push('/expenses');
            } catch (error) {
                console.error('Erro ao salvar despesa:', error);
                toast.error(error.message || 'Erro ao salvar despesa');
            } finally {
                loading.value = false;
            }
        };

        onMounted(async () => {
            await loadUserData();
            await loadCategories();
            
            if (route.params.id) {
                await loadExpense();
            }
        });

        return {
            user,
            isEditing,
            form,
            saveExpense,
            categories,
            errors,
            loading
        };
    }
};
</script> 