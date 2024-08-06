<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-white to-indigo-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-indigo-600 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zm0 0V7m0 4v4m0 0c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4z"/></svg>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Entre na sua conta
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="handleLogin" autocomplete="on">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" v-model="form.email" type="email" required autofocus
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 shadow-sm"
                            placeholder="seu@email.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input id="password" v-model="form.password" type="password" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>
                <div v-if="error" class="text-red-600 text-sm text-center mt-2">{{ error }}</div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 shadow transition">
                        Entrar
                    </button>
                </div>
                <div class="text-sm text-center mt-2">
                    <router-link to="/register" class="font-medium text-indigo-600 hover:text-indigo-500 transition">
                        Não tem uma conta? <span class="underline">Registre-se</span>
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Login',
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            error: ''
        }
    },
    methods: {
        async handleLogin() {
            this.error = '';
            try {
                // Primeiro, obtém o CSRF token
                await fetch('/sanctum/csrf-cookie', {
                    credentials: 'include'
                });

                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'include',
                    body: JSON.stringify(this.form)
                });

                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        this.error = data.message || 'Credenciais inválidas. Tente novamente.';
                    } else {
                        this.error = 'Erro ao conectar. Tente novamente.';
                    }
                    return;
                }

                const data = await response.json();
                if (!data.token) {
                    this.error = 'Token não recebido. Tente novamente.';
                    return;
                }

                localStorage.setItem('token', data.token);
                this.$router.push('/dashboard');
            } catch (error) {
                console.error('Erro:', error);
                this.error = 'Erro ao conectar. Tente novamente.';
            }
        }
    }
}
</script> 