import DashboardScreen from '@/fitness/views/DashboardScreen.vue';
import LoginScreen from '@/fitness/views/LoginScreen.vue';
import ClientsScreen from '@/fitness/views/ClientsScreen.vue';

const routes = [
    {
        path: '/fit',
        component: DashboardScreen,
    },
    {
        path: '/',
        component: DashboardScreen,
    },
    {
        path: '/clients',
        component: ClientsScreen,
    },
    {
        path: '/login',
        component: LoginScreen,
        meta: {
            CAN_ENTER: {
                IS_GUEST: true,
            },
        },
    },
];

export default routes;
