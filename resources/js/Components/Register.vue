<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-white to-indigo-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-indigo-600 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Crie sua conta
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="handleRegister" autocomplete="on">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input id="name" v-model="form.name" type="text" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 shadow-sm"
                            placeholder="Seu nome completo">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" v-model="form.email" type="email" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 shadow-sm"
                            placeholder="seu@email.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input id="password" v-model="form.password" type="password" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 shadow-sm"
                            placeholder="Crie uma senha">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 shadow-sm"
                            placeholder="Repita a senha">
                    </div>
                </div>
                <div v-if="error" class="text-red-600 text-sm text-center mt-2">{{ error }}</div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 shadow transition">
                        Registrar
                    </button>
                </div>
                <div class="text-sm text-center mt-2">
                    <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500 transition">
                        Já tem uma conta? <span class="underline">Entre aqui</span>
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Register',
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            error: ''
        }
    },
    methods: {
        async handleRegister() {
            this.error = '';
            if (this.form.password !== this.form.password_confirmation) {
                this.error = 'As senhas não coincidem.';
                return;
            }

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                    },
                    body: JSON.stringify(this.form)
                });

                if (!response.ok) {
                    this.error = 'Erro ao registrar. Verifique os dados e tente novamente.';
                    return;
                }

                const data = await response.json();
                localStorage.setItem('token', data.token);
                this.$router.push('/dashboard');
            } catch (error) {
                this.error = 'Erro ao conectar. Tente novamente.';
            }
        }
    }
}
</script> 