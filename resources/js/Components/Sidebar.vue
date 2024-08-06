<template>
    <div>
        <div class="lg:hidden fixed top-0 left-0 z-50 w-full bg-white shadow-sm">
            <button @click="isOpen = !isOpen" class="p-4 text-gray-500 hover:text-gray-600 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div :class="[
            'fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-indigo-600 to-indigo-800 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full'
        ]">
            <div class="flex items-center justify-center h-16 px-4 bg-indigo-700">
                <h1 class="text-xl font-bold">MEI Organizer</h1>
            </div>

            <div class="px-4 py-6 border-b border-indigo-500">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center">
                            <span class="text-lg font-medium">{{ userInitials }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ user?.name }}</p>
                        <p class="text-xs text-indigo-200">{{ user?.email }}</p>
                    </div>
                </div>
            </div>

            <nav class="mt-6">
                <div class="px-4 space-y-1">
                    <router-link 
                        v-for="item in navigation" 
                        :key="item.name"
                        :to="item.href"
                        :class="[
                            $route.path === item.href
                                ? 'bg-indigo-700 text-white'
                                : 'text-indigo-100 hover:bg-indigo-700 hover:text-white',
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150'
                        ]"
                    >
                        <span 
                            v-html="item.icon"
                            :class="[
                                $route.path === item.href ? 'text-white' : 'text-indigo-300 group-hover:text-white',
                                'mr-3 flex-shrink-0 h-5 w-5'
                            ]"
                        ></span>
                        {{ item.name }}
                    </router-link>
                </div>
            </nav>

            <div class="absolute bottom-0 w-full p-4">
                <button 
                    @click="logout" 
                    class="w-full flex items-center px-2 py-2 text-sm font-medium text-indigo-100 hover:bg-indigo-700 hover:text-white rounded-md transition-colors duration-150"
                >
                    <svg class="mr-3 h-5 w-5 text-indigo-300 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sair
                </button>
            </div>
        </div>

        <div 
            v-if="isOpen" 
            class="fixed inset-0 z-30 bg-gray-600 bg-opacity-75 lg:hidden"
            @click="isOpen = false"
        ></div>
    </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

export default {
    name: 'Sidebar',
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const router = useRouter();
        const toast = useToast();
        const isOpen = ref(false);

        const userInitials = computed(() => {
            if (!props.user?.name) return '';
            return props.user.name
                .split(' ')
                .map(word => word[0])
                .join('')
                .toUpperCase()
                .slice(0, 2);
        });

        const navigation = [
            {
                name: 'Dashboard',
                href: '/dashboard',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>`
            },
            {
                name: 'Notas Fiscais',
                href: '/invoices',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>`
            },
            {
                name: 'Empresas',
                href: '/partner-companies',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>`
            },
            {
                name: 'Categorias de Despesas',
                href: '/expense-categories',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>`
            },
            {
                name: 'Despesas',
                href: '/expenses',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`
            },
            {
                name: 'Preferências',
                href: '/preferences',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>`
            },
            {
                name: 'Meu Perfil',
                href: '/profile',
                icon: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>`
            }
        ];

        const logout = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const response = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao fazer logout');
                }

                localStorage.removeItem('token');
                router.push('/login');
                toast.success('Logout realizado com sucesso!');
            } catch (error) {
                console.error('Erro ao fazer logout:', error);
                toast.error(error.message || 'Erro ao fazer logout');
            }
        };

        return {
            isOpen,
            navigation,
            userInitials,
            logout
        };
    }
};
</script> 