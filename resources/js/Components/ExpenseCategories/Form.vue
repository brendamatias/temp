<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="lg:pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ isEditing ? 'Editar Categoria' : 'Nova Categoria' }}
                        </h1>
                        <router-link 
                            to="/expense-categories"
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
                            <form @submit.prevent="saveCategory" class="space-y-8 divide-y divide-gray-200">
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Dados da Categoria</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Informações da categoria de despesa.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-4">
                                            <label for="name" class="block text-sm font-medium text-gray-700">
                                                Nome
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

                                        <div class="sm:col-span-6">
                                            <label for="description" class="block text-sm font-medium text-gray-700">
                                                Descrição
                                            </label>
                                            <div class="mt-1">
                                                <textarea 
                                                    id="description"
                                                    v-model="form.description"
                                                    rows="3"
                                                    class="block w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.description }"
                                                ></textarea>
                                                <p v-if="errors.description" class="mt-2 text-sm text-red-600">
                                                    {{ errors.description[0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-6">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input 
                                                        type="checkbox" 
                                                        id="is_active"
                                                        v-model="form.is_active"
                                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                    />
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="is_active" class="font-medium text-gray-700">
                                                        Categoria Ativa
                                                    </label>
                                                    <p class="text-gray-500">
                                                        Desmarque esta opção para desativar a categoria.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8">
                                    <div class="flex justify-end space-x-3">
                                        <router-link 
                                            to="/expense-categories"
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
                                            {{ isEditing ? 'Salvar Alterações' : 'Criar Categoria' }}
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
    name: 'ExpenseCategoryForm',
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
            description: '',
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

        const loadCategory = async () => {
            if (!isEditing.value) return;

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch(`/api/expense-categories/${route.params.id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar categoria');
                }

                const data = await response.json();
                form.value = {
                    name: data.name,
                    description: data.description,
                    is_active: data.is_active
                };
            } catch (error) {
                console.error('Erro ao carregar categoria:', error);
                toast.error(error.message || 'Erro ao carregar categoria');
                router.push('/expense-categories');
            }
        };

        const saveCategory = async () => {
            loading.value = true;
            errors.value = {};

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const url = isEditing.value 
                    ? `/api/expense-categories/${route.params.id}`
                    : '/api/expense-categories';

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
                    throw new Error(data.message || 'Erro ao salvar categoria');
                }

                toast.success(isEditing.value 
                    ? 'Categoria atualizada com sucesso!' 
                    : 'Categoria criada com sucesso!'
                );
                router.push('/expense-categories');
            } catch (error) {
                console.error('Erro ao salvar categoria:', error);
                toast.error(error.message);
            } finally {
                loading.value = false;
            }
        };

        onMounted(async () => {
            await loadUserData();
            await loadCategory();
        });

        return {
            user,
            form,
            errors,
            loading,
            isEditing,
            saveCategory
        };
    }
};
</script> 