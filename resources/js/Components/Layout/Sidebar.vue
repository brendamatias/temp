<template>
  <div class="flex flex-col h-full bg-gray-800">
    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
      <div class="flex items-center flex-shrink-0 px-4">
        <img class="h-8 w-auto" src="/logo.svg" alt="MEI Organizer" />
      </div>
      <nav class="mt-5 flex-1 px-2 space-y-1">
        <router-link
          to="/"
          class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          :class="[
            $route.path === '/'
              ? 'bg-gray-900 text-white'
              : 'text-gray-300 hover:bg-gray-700 hover:text-white'
          ]"
        >
          <svg
            class="mr-3 h-6 w-6"
            :class="[
              $route.path === '/'
                ? 'text-gray-300'
                : 'text-gray-400 group-hover:text-gray-300'
            ]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
            />
          </svg>
          Dashboard
        </router-link>

        <router-link
          to="/invoices"
          class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          :class="[
            $route.path === '/invoices'
              ? 'bg-gray-900 text-white'
              : 'text-gray-300 hover:bg-gray-700 hover:text-white'
          ]"
        >
          <svg
            class="mr-3 h-6 w-6"
            :class="[
              $route.path === '/invoices'
                ? 'text-gray-300'
                : 'text-gray-400 group-hover:text-gray-300'
            ]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          Notas Fiscais
        </router-link>

        <router-link
          to="/expenses"
          class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          :class="[
            $route.path === '/expenses'
              ? 'bg-gray-900 text-white'
              : 'text-gray-300 hover:bg-gray-700 hover:text-white'
          ]"
        >
          <svg
            class="mr-3 h-6 w-6"
            :class="[
              $route.path === '/expenses'
                ? 'text-gray-300'
                : 'text-gray-400 group-hover:text-gray-300'
            ]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
            />
          </svg>
          Despesas
        </router-link>

        <router-link
          to="/reports"
          class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          :class="[
            $route.path === '/reports'
              ? 'bg-gray-900 text-white'
              : 'text-gray-300 hover:bg-gray-700 hover:text-white'
          ]"
        >
          <svg
            class="mr-3 h-6 w-6"
            :class="[
              $route.path === '/reports'
                ? 'text-gray-300'
                : 'text-gray-400 group-hover:text-gray-300'
            ]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            />
          </svg>
          Relatórios
        </router-link>

        <router-link
          to="/preferences"
          class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          :class="[
            $route.path === '/preferences'
              ? 'bg-gray-900 text-white'
              : 'text-gray-300 hover:bg-gray-700 hover:text-white'
          ]"
        >
          <svg
            class="mr-3 h-6 w-6"
            :class="[
              $route.path === '/preferences'
                ? 'text-gray-300'
                : 'text-gray-400 group-hover:text-gray-300'
            ]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
          Preferências
        </router-link>
      </nav>
    </div>
    <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
      <button
        @click="logout"
        class="flex-shrink-0 w-full group block"
      >
        <div class="flex items-center">
          <div>
            <svg
              class="inline-block h-10 w-10 rounded-full text-gray-400"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-base font-medium text-white">
              {{ user?.name }}
            </p>
            <p class="text-sm font-medium text-gray-400 group-hover:text-gray-300">
              Sair
            </p>
          </div>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref(JSON.parse(localStorage.getItem('user')))

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script> 