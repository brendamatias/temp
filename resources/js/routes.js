import InvoiceForm from './Components/Invoices/Form.vue';
import InvoiceList from './Components/Invoices/Index.vue';
import PartnerCompanyForm from './Components/PartnerCompanies/Form.vue';
import PartnerCompanyList from './Components/PartnerCompanies/Index.vue';
import ExpenseCategoryForm from './Components/ExpenseCategories/Form.vue';
import ExpenseCategoryList from './Components/ExpenseCategories/Index.vue';
import ExpenseForm from './Components/Expenses/Form.vue';
import ExpenseList from './Components/Expenses/Index.vue';
import Login from './Components/Auth/Login.vue';
import Register from './Components/Auth/Register.vue';
import Dashboard from './Components/Dashboard.vue';
import Profile from './views/Profile.vue'

const routes = [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guest: true }
    },
    {
        path: '/invoices',
        name: 'invoices.index',
        component: InvoiceList,
        meta: { requiresAuth: true }
    },
    {
        path: '/invoices/create',
        name: 'invoices.create',
        component: InvoiceForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/invoices/:id/edit',
        name: 'invoices.edit',
        component: InvoiceForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/partner-companies',
        name: 'partner-companies.index',
        component: PartnerCompanyList,
        meta: { requiresAuth: true }
    },
    {
        path: '/partner-companies/create',
        name: 'partner-companies.create',
        component: PartnerCompanyForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/partner-companies/:id/edit',
        name: 'partner-companies.edit',
        component: PartnerCompanyForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expense-categories',
        name: 'expense-categories.index',
        component: ExpenseCategoryList,
        meta: { requiresAuth: true }
    },
    {
        path: '/expense-categories/create',
        name: 'expense-categories.create',
        component: ExpenseCategoryForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expense-categories/:id/edit',
        name: 'expense-categories.edit',
        component: ExpenseCategoryForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expenses',
        name: 'expenses.index',
        component: ExpenseList,
        meta: { requiresAuth: true }
    },
    {
        path: '/expenses/create',
        name: 'expenses.create',
        component: ExpenseForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/expenses/:id/edit',
        name: 'expenses.edit',
        component: ExpenseForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/dashboard'
    }
];

export default routes; 