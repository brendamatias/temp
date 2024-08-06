<template>
    <div class="min-h-screen bg-gray-100">
        <Sidebar :user="user" v-if="showSidebar" />
        <div :class="['lg:pl-64', { 'pt-16': showSidebar }]">
            <router-view></router-view>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './Components/Sidebar.vue'

const route = useRoute()
const user = ref(null)

const showSidebar = computed(() => {
    return !['login', 'register'].includes(route.name)
})
</script> 