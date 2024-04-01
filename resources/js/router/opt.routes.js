import ProductsIndex from '@/views/Opt/Products/ProductsIndex.vue';
import ProductsCreate from '@/views/Opt/Products/ProductsCreate.vue';
import ProductsEdit from '@/views/Opt/Products/ProductsEdit.vue';
import OrdersIndex from '@/views/Opt/Orders/OrdersIndex.vue';
import ClientsIndex from '@/views/Opt/Clients/ClientsIndex.vue';
import OrdersShow from '@/views/Opt/Orders/OrdersShow.vue';

export default [
    {
        path: '/opt/products',
        component: ProductsIndex,
    },
    {
        path: '/opt/products/create',
        component: ProductsCreate,
    },
    {
        path: '/opt/products/edit/:id',
        component: ProductsEdit,
    },
    {
        path: '/opt/orders',
        component: OrdersIndex,
    },
    {
        path: '/opt/orders/:id',
        component: OrdersShow,
    },
    {
        path: '/opt/clients',
        component: ClientsIndex,
    },
];
