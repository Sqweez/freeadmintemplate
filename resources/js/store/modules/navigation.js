const navigationModule = {
    state: {
        menu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Продавцы',
                url: '/users',
                icon: 'person',
                isAdmin: true
            },
            {
                title: 'Страница наблюдателя',
                url: '/observer',
                icon: 'dashboard',
                isObserver: true,
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person'
            },
            {
                title: 'Склад',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Все склады',
                        url: '/stores',
                        isAdmin: true
                    },
                    {
                        title: 'Все товары',
                        url: '/products'
                    },
                    {
                        title: 'Категории',
                        url: '/categories',
                        isAdmin: true
                    },
                    {
                        title: 'Корзина',
                        url: '/cart'
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
                        //isAdmin: true
                    },
                    {
                        title: 'План продаж',
                        url: '/plan',
                        isAdmin: true
                    },
                    {
                        title: 'Ревизии',
                        url: '/revision',
                    },
                    {
                        title: 'Поступления',
                        url: '/arrivals',
                        isAdmin: true,
                    },
                ],
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report',
                isAdmin: true
            },
            {
                title: 'Интернет-магазин',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Цели',
                        url: '/shop/goals'
                    },
                    {
                        title: 'Атлеты',
                        url: '/shop/sportsmen'
                    },
                    {
                        title: 'Рейтинг продавцов',
                        url: '/shop/rating'
                    }
                ]
            },
            {
                title: 'Статистика',
                url: '#',
                icon: 'dashboard',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/stats/mvp_products',
                    }
                ]
            }
        ],
    },
    getters: {
        navigations: state => state.menu,
        ADMIN_NAVIGATIONS: state => state.menu,
        OBSERVER_NAVIGATIONS: state => state.menu.filter(s => s.isObserver),
        SELLER_NAVIGATIONS: state => state.menu
            .filter(s => !s.isObserver && !s.isAdmin)
            .map(s => {
                let children = [];
                if (s.children) {
                    children = s.children.filter(c => !c.isAdmin && !c.isObserver);
                    return {...s, children};
                }
                return s;
            })
    }
};

export default navigationModule;
