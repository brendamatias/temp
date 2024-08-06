<template>
    <div class="min-h-screen bg-gray-50">
        <Sidebar v-if="user" :user="user" />
        <div class="pl-64">
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-gray-900">Relatórios</h1>
                    </div>
                </div>
            </header>

            <main class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                                    <input 
                                        type="date" 
                                        id="start_date" 
                                        v-model="filters.start_date"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                                    <input 
                                        type="date" 
                                        id="end_date" 
                                        v-model="filters.end_date"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo de Relatório</label>
                                    <select 
                                        id="report_type" 
                                        v-model="filters.report_type"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                        <option value="monthly">Mensal</option>
                                        <option value="quarterly">Trimestral</option>
                                        <option value="yearly">Anual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button 
                                    @click="generateReport"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Gerar Relatório
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="report" class="mt-8 space-y-8">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Resumo</h3>
                                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                                    <div class="bg-white overflow-hidden shadow rounded-lg">
                                        <div class="px-4 py-5 sm:p-6">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Faturas</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ report.total_invoices }}</dd>
                                        </div>
                                    </div>

                                    <div class="bg-white overflow-hidden shadow rounded-lg">
                                        <div class="px-4 py-5 sm:p-6">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Despesas</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ report.total_expenses }}</dd>
                                        </div>
                                    </div>

                                    <div class="bg-white overflow-hidden shadow rounded-lg">
                                        <div class="px-4 py-5 sm:p-6">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Saldo</dt>
                                            <dd class="mt-1 text-3xl font-semibold" :class="report.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                                {{ formatCurrency(report.balance) }}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Faturas vs Despesas</h3>
                                    <div class="mt-4">
                                        <canvas ref="invoicesVsExpensesChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Categorias de Despesas</h3>
                                    <div class="mt-4">
                                        <canvas ref="expenseCategoriesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Últimas Faturas</h3>
                                    <div class="mt-4">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="invoice in report.recent_invoices" :key="invoice.id">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ invoice.client }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(invoice.date) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(invoice.value) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span 
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                            :class="{
                                                                'bg-green-100 text-green-800': invoice.status === 'paid',
                                                                'bg-yellow-100 text-yellow-800': invoice.status === 'pending',
                                                                'bg-red-100 text-red-800': invoice.status === 'overdue'
                                                            }"
                                                        >
                                                            {{ formatStatus(invoice.status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Últimas Despesas</h3>
                                    <div class="mt-4">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="expense in report.recent_expenses" :key="expense.id">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ expense.description }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(expense.date) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCategory(expense.category) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(expense.value) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { Chart } from 'chart.js/auto';
import Sidebar from '../Sidebar.vue';

export default {
    components: {
        Sidebar
    },
    setup() {
        const user = ref(null);
        const report = ref(null);
        const filters = ref({
            start_date: '',
            end_date: '',
            report_type: 'monthly'
        });

        const invoicesVsExpensesChart = ref(null);
        const expenseCategoriesChart = ref(null);

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

        const generateReport = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token não encontrado');
                }

                const queryParams = new URLSearchParams({
                    start_date: filters.value.start_date,
                    end_date: filters.value.end_date,
                    report_type: filters.value.report_type
                });

                const response = await fetch(`/api/reports?${queryParams}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao gerar relatório');
                }

                const data = await response.json();
                report.value = data;

                // Inicializar gráficos após receber os dados
                initializeCharts();
            } catch (error) {
                console.error('Erro ao gerar relatório:', error);
            }
        };

        const initializeCharts = () => {
            if (!report.value) return;

            // Gráfico de Faturas vs Despesas
            if (invoicesVsExpensesChart.value) {
                invoicesVsExpensesChart.value.destroy();
            }

            const invoicesVsExpensesCtx = document.getElementById('invoicesVsExpensesChart');
            invoicesVsExpensesChart.value = new Chart(invoicesVsExpensesCtx, {
                type: 'bar',
                data: {
                    labels: report.value.months,
                    datasets: [
                        {
                            label: 'Faturas',
                            data: report.value.invoices,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        },
                        {
                            label: 'Despesas',
                            data: report.value.expenses,
                            backgroundColor: 'rgba(239, 68, 68, 0.5)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Categorias de Despesas
            if (expenseCategoriesChart.value) {
                expenseCategoriesChart.value.destroy();
            }

            const expenseCategoriesCtx = document.getElementById('expenseCategoriesChart');
            expenseCategoriesChart.value = new Chart(expenseCategoriesCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(report.value.expense_categories).map(formatCategory),
                    datasets: [{
                        data: Object.values(report.value.expense_categories),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.5)',
                            'rgba(239, 68, 68, 0.5)',
                            'rgba(16, 185, 129, 0.5)',
                            'rgba(245, 158, 11, 0.5)',
                            'rgba(139, 92, 246, 0.5)',
                            'rgba(236, 72, 153, 0.5)',
                            'rgba(107, 114, 128, 0.5)'
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(239, 68, 68)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(139, 92, 246)',
                            'rgb(236, 72, 153)',
                            'rgb(107, 114, 128)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('pt-BR');
        };

        const formatCurrency = (value) => {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        };

        const formatStatus = (status) => {
            const statusMap = {
                'paid': 'Pago',
                'pending': 'Pendente',
                'overdue': 'Atrasado'
            };
            return statusMap[status] || status;
        };

        const formatCategory = (category) => {
            const categoryMap = {
                'alimentacao': 'Alimentação',
                'transporte': 'Transporte',
                'moradia': 'Moradia',
                'saude': 'Saúde',
                'educacao': 'Educação',
                'lazer': 'Lazer',
                'outros': 'Outros'
            };
            return categoryMap[category] || category;
        };

        onMounted(async () => {
            await loadUserData();
            
            // Definir datas padrão (último mês)
            const today = new Date();
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            filters.value.start_date = lastMonth.toISOString().split('T')[0];
            filters.value.end_date = today.toISOString().split('T')[0];
            
            // Gerar relatório inicial
            await generateReport();
        });

        return {
            user,
            report,
            filters,
            generateReport,
            formatDate,
            formatCurrency,
            formatStatus,
            formatCategory
        };
    }
};
</script> 