import Dashboard from "../views/Dashboard/Dashboard";
import Users from "../views/Users/Users";
import Stores from "../views/Stores/Stores";
import Products from "../views/Products/Products";
import Control from "../views/Control/Control";
import Cart from "../views/Cart/Cart";
import Clients from "../views/Clients/Clients";
import Reports from "../views/Reports/Reports";
import Transfers from "../views/Transfers/Transfers";
import Hits from '../views/Hits/Hits';

const routes = [
    {
        path: '/',
        component: Dashboard
    },
    {
        path: '/users',
        component: Users
    },
    {
        path: '/stores',
        component: Stores
    },
    {
        path: '/products',
        component: Products
    },
    {
        path: '/categories',
        component: Control
    },
    {
        path: '/cart',
        component: Cart
    },
    {
        path: '/clients',
        component: Clients
    },
    {
        path: '/reports',
        component: Reports
    },
    {
        path: '/transfer',
        component: Transfers
    },
    {
        path: '/shop/products',
        component: Hits
    }
];

export default routes;
