<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Entre na sua conta
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="login">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input 
                            id="email" 
                            v-model="form.email"
                            type="email" 
                            required 
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                            placeholder="Email"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Senha</label>
                        <input 
                            id="password" 
                            v-model="form.password"
                            type="password" 
                            required 
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                            placeholder="Senha"
                        />
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Entrar
                    </button>
                </div>

                <div class="text-sm text-center">
                    <router-link to="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Não tem uma conta? Registre-se
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

export default {
    name: 'Login',
    setup() {
        const router = useRouter();
        const toast = useToast();
        const form = ref({
            email: '',
            password: ''
        });

        const login = async () => {
            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(form.value)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao fazer login');
                }

                localStorage.setItem('token', data.token);
                toast.success('Login realizado com sucesso!');
                router.push({ name: 'dashboard' });
            } catch (error) {
                console.error('Erro ao fazer login:', error);
                toast.error(error.message || 'Erro ao fazer login');
            }
        };

        return {
            form,
            login
        };
    }
};
</script> 