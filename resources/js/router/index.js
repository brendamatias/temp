import { createRouter, createWebHistory } from 'vue-router';
import Login from '../Components/Login.vue';
import Register from '../views/auth/Register.vue';
import Dashboard from '../Components/Dashboard.vue';
import InvoicesIndex from '../Components/Invoices/Index.vue';
import InvoicesForm from '../Components/Invoices/Form.vue';
import ExpensesIndex from '../Components/Expenses/Index.vue';
import ExpensesForm from '../Components/Expenses/Form.vue';
import ReportsIndex from '../Components/Reports/Index.vue';
import PartnerCompaniesIndex from '../Components/PartnerCompanies/Index.vue';
import PartnerCompaniesForm from '../Components/PartnerCompanies/Form.vue';
import ExpenseCategoriesIndex from '../Components/ExpenseCategories/Index.vue';
import ExpenseCategoriesForm from '../Components/ExpenseCategories/Form.vue';
import Preferences from '../Components/Preferences.vue';
import Profile from '../views/Profile.vue';

const routes = [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresAuth: false }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { requiresAuth: false }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/invoices',
        name: 'invoices.index',
        component: InvoicesIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/invoices/create',
        name: 'invoices.create',
        component: InvoicesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/invoices/:id/edit',
        name: 'invoices.edit',
        component: InvoicesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expenses',
        name: 'expenses.index',
        component: ExpensesIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/expenses/create',
        name: 'expenses.create',
        component: ExpensesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expenses/:id/edit',
        name: 'expenses.edit',
        component: ExpensesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/reports',
        name: 'reports.index',
        component: ReportsIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/preferences',
        name: 'preferences',
        component: Preferences,
        meta: { requiresAuth: true }
    },
    {
        path: '/partner-companies',
        name: 'partner-companies.index',
        component: PartnerCompaniesIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/partner-companies/create',
        name: 'partner-companies.create',
        component: PartnerCompaniesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/partner-companies/:id/edit',
        name: 'partner-companies.edit',
        component: PartnerCompaniesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expense-categories',
        name: 'expense-categories.index',
        component: ExpenseCategoriesIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/expense-categories/create',
        name: 'expense-categories.create',
        component: ExpenseCategoriesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expense-categories/:id/edit',
        name: 'expense-categories.edit',
        component: ExpenseCategoriesForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

    if (requiresAuth && !token) {
        next('/login');
    } else if (to.path === '/login' && token) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router; 