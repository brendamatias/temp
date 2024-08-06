<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <Sidebar />
        
        <div class="py-10">
            <header>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white">
                        Dashboard
                    </h1>
                </div>
            </header>
            <main>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <button
                            @click="$router.push('/invoices/create')"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Lançar Nota Fiscal
                        </button>
                        <button
                            @click="$router.push('/expenses/create')"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Lançar Despesa
                        </button>
                    </div>

                    <div class="mb-6">
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ano
                        </label>
                        <select
                            id="year"
                            v-model="selectedYear"
                            @change="loadDashboardData"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                        >
                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Limite MEI Disponível</h3>
                                <div class="mt-2">
                                    <div class="relative pt-1">
                                        <div class="flex mb-2 items-center justify-between">
                                            <div>
                                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200">
                                                    {{ formatCurrency(meiLimit.available) }} disponíveis
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs font-semibold inline-block text-indigo-600 dark:text-indigo-400">
                                                    {{ formatCurrency(meiLimit.total) }} total
                                                </span>
                                            </div>
                                        </div>
                                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-200 dark:bg-indigo-900">
                                            <div
                                                :style="{ width: `${(meiLimit.used / meiLimit.total) * 100}%` }"
                                                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500"
                                            ></div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Você ainda pode emitir notas fiscais no valor de {{ formatCurrency(meiLimit.available) }} sem se desenquadrar como MEI.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Despesas por Categoria</h3>
                                <div class="mt-2">
                                    <canvas ref="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Receitas Mensais</h3>
                                <div class="mt-2">
                                    <canvas ref="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Despesas Mensais</h3>
                                <div class="mt-2">
                                    <canvas ref="expensesChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg lg:col-span-2">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Balanço Mensal (Receitas - Despesas)</h3>
                                <div class="mt-2">
                                    <canvas ref="balanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import Chart from 'chart.js/auto'
import Sidebar from './Sidebar.vue'

const router = useRouter()
const selectedYear = ref(new Date().getFullYear())
const availableYears = ref([])
const meiLimit = ref({
    total: 81000,
    used: 0,
    available: 81000
})

const categoryChart = ref(null)
const revenueChart = ref(null)
const expensesChart = ref(null)
const balanceChart = ref(null)

let categoryChartInstance = null
let revenueChartInstance = null
let expensesChartInstance = null
let balanceChartInstance = null

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}

const loadDashboardData = async () => {
    try {
        const token = localStorage.getItem('token')
        if (!token) {
            router.push('/login')
            return
        }

        const response = await fetch(`/api/dashboard?year=${selectedYear.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })

        if (!response.ok) {
            throw new Error('Erro ao carregar dados do dashboard')
        }

        const data = await response.json()
        
        meiLimit.value = data.meiLimit

        updateCategoryChart(data.categoryExpenses)
        updateRevenueChart(data.monthlyRevenue)
        updateExpensesChart(data.monthlyExpenses)
        updateBalanceChart(data.monthlyBalance)
    } catch (error) {
        console.error('Erro ao carregar dados do dashboard:', error)
    }
}

const updateCategoryChart = (data) => {
    if (categoryChartInstance) {
        categoryChartInstance.destroy()
    }

    categoryChartInstance = new Chart(categoryChart.value, {
        type: 'pie',
        data: {
            labels: data.map(item => item.category),
            datasets: [{
                data: data.map(item => item.value),
                backgroundColor: [
                    '#4F46E5', '#7C3AED', '#EC4899', '#F59E0B', '#10B981',
                    '#3B82F6', '#8B5CF6', '#F43F5E', '#F97316', '#14B8A6'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            return `${context.label}: ${formatCurrency(value)}`;
                        }
                    }
                }
            }
        }
    })
}

const updateRevenueChart = (data) => {
    if (revenueChartInstance) {
        revenueChartInstance.destroy()
    }

    revenueChartInstance = new Chart(revenueChart.value, {
        type: 'bar',
        data: {
            labels: data.map(item => item.month),
            datasets: [{
                label: 'Valor das Notas Fiscais',
                data: data.map(item => item.value),
                backgroundColor: '#4F46E5'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => formatCurrency(value)
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            return `Valor: ${formatCurrency(value)}`;
                        }
                    }
                }
            }
        }
    })
}

const updateExpensesChart = (data) => {
    if (expensesChartInstance) {
        expensesChartInstance.destroy()
    }

    expensesChartInstance = new Chart(expensesChart.value, {
        type: 'bar',
        data: {
            labels: data.map(item => item.month),
            datasets: [{
                label: 'Valor das Despesas',
                data: data.map(item => item.value),
                backgroundColor: '#F43F5E'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => formatCurrency(value)
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            return `Valor: ${formatCurrency(value)}`;
                        }
                    }
                }
            }
        }
    })
}

const updateBalanceChart = (data) => {
    if (balanceChartInstance) {
        balanceChartInstance.destroy()
    }

    balanceChartInstance = new Chart(balanceChart.value, {
        type: 'line',
        data: {
            labels: data.map(item => item.month),
            datasets: [{
                label: 'Balanço (Receitas - Despesas)',
                data: data.map(item => item.value),
                borderColor: '#10B981',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(16, 185, 129, 0.1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        callback: value => formatCurrency(value)
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            return `Balanço: ${formatCurrency(value)}`;
                        }
                    }
                }
            }
        }
    })
}

const generateAvailableYears = () => {
    const currentYear = new Date().getFullYear()
    availableYears.value = Array.from({ length: 11 }, (_, i) => currentYear - 5 + i)
}

onMounted(() => {
    generateAvailableYears()
    loadDashboardData()
})

watch(selectedYear, () => {
    loadDashboardData()
})
</script> 