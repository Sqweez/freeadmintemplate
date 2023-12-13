import DashboardScreen from '@/fitness/views/DashboardScreen.vue';
import LoginScreen from '@/fitness/views/LoginScreen.vue';

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
