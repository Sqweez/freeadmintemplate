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
import Login from "../views/Login/Login";
import Goals from "../views/Goals/Goals";
import Sportsmen from "../views/Sportsmen/Sportsmen";
import Plan from "../views/Plan/Plan";
import MVPProducts from "../views/MVPProducts/MVPProducts";
import Rating from "../views/Rating/Rating";
import Revision from "../views/Revision/Revision";

const routes = [
    {
        path: '/',
        component: Dashboard
    },
    {
        path: '/users',
        component: Users,
        meta: {
            isAdmin: true
        }
    },
    {
        path: '/stores',
        component: Stores,
        meta: {
            isAdmin: true
        }

    },
    {
        path: '/products',
        component: Products
    },
    {
        path: '/categories',
        component: Control,
        meta: {
            isAdmin: true
        }
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
        component: Reports,
        meta: {
            isAdmin: true
        }
    },
    {
        path: '/plan',
        component: Plan,
        meta: {
            isAdmin: true
        }
    },
    {
        path: '/transfer',
        component: Transfers
    },
    {
        path: '/shop/products',
        component: Hits
    },
    {
        path: '/shop/goals',
        component: Goals
    },
    {
        path: '/shop/sportsmen',
        component: Sportsmen
    },
    {
        path: '/login',
        component: Login,
        meta: {
            guest: true
        }
    },
    {
        path: '/stats/mvp_products',
        component: MVPProducts,
        meta: {
            isAdmin: true
        }
    },
    {
        path: '/shop/rating',
        component: Rating,
        meta: {
            isAdmin: true
        }
    },
    {
        path: '/revision',
        component: Revision
    }
];

export default routes;
