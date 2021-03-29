const navigationModule = {
    state: {
        partner_sellersMenu: [
            {
                title: 'Главная',
                icon: 'dashboard',
                url: '/'
            },
            {
                title: 'Корзина',
                icon: 'store',
                url: '/cart/partner'
            },
            {
                title: 'Товары',
                icon: 'dashboard',
                url: '/companion/products'
            },
        ],
        moderatorMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Баннеры',
                url: '/shop/banners',
                icon: 'dashboard',
            }
        ],
        supplierMenu: [
            {
                title: 'Отчеты по продажам',
                url: '/supplier/reports',
                icon: 'dashboard'
            }
        ],
        adminMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Пользователи',
                url: '/users',
                icon: 'person',
                isAdmin: true
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
                        title: 'Категории',
                        url: '/categories',
                        isAdmin: true
                    },
                    {
                        title: 'План продаж',
                        url: '/plan',
                        isAdmin: true
                    },
                    {
                        title: 'Товары',
                        url: '/products'
                    },
                    {
                        title: 'Корзина',
                        url: '/cart'
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
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
            },
            {
                title: 'Отчеты по товарам',
                url: '/reports/products',
                icon: 'report',
            },
            {
                title: 'Kaspi',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/kaspi/products'
                    },
                    {
                        title: 'Заказы',
                        url: '/kaspi/orders'
                    }
                ]
            },
            {
                title: 'Партнеры',
                url: '#',
                isAdmin: true,
                hasDropdown: true,
                children: [
                    {
                        title: 'Закупы',
                        url: '/companions/transfer'
                    }
                ],
            },
            {
                title: 'Модератор',
                url: '#',
                icon: 'dashboard',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/moderator/products'
                    },
                    {
                        title: 'Новости',
                        url: '/moderator/news'
                    },
                ]
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
                    },
                    {
                        title: 'Промокоды',
                        url: '/promocode'
                    },
                    {
                        title: 'Баннеры',
                        url: '/shop/banners'
                    },
                    {
                        title: "Связанные товары",
                        url: '/shop/related'
                    },
                    {
                        title: "Заказы",
                        url: '/shop/orders'
                    }
                ]
            },
   /*         {
                title: 'Статистика',
                url: '#',
                icon: 'dashboard',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/stats/mvp_products',
                    },
                    {
                        title: 'Партнеры',
                        url: '/stats/partners'
                    }
                ]
            },
            {
                title: 'v2/Склад',
                url: '#',
                icon: 'dashboard',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/v2/products'
                    },
                ]
            },*/
           /* {
                title: 'v3/Склад',
                url: '#',
                icon: 'dashboard',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/v3/products'
                    },
                    {
                        title: 'Корзина',
                        url: '/v3/cart'
                    },
                    {
                        title: 'Отчеты',
                        url: '/v3/reports'
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
                    },
                    {
                        title: 'Поступления',
                        url: '/arrivals',
                        isAdmin: true,
                    },
                ]
            },*/
        ],
        sellerMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
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
                        title: 'Корзина',
                        url: '/cart'
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
                    },

                ],
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report',
            },
            {
                title: "Заказы с интернет-ммгазина",
                url: '/shop/orders'
            }

        ],
    },
    getters: {
        navigations: (state, getters) => {
            const ROLE = getters.CURRENT_ROLE;
            console.log(ROLE);
            return state[`${ROLE}Menu`];
        }
    }
};

export default navigationModule;
