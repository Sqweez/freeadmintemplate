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
        storekeeperMenu: [
            {
                title: 'Перемещения',
                url: '/transfer',
                icon: 'work',
            },
            {
                title: 'Поступления',
                url: '/arrivals',
                icon: 'work',
            },
            {
                title: 'Товары',
                url: '/products',
                icon: 'dashboard'
            },
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
                title: 'Документооборот',
                url: '#',
                icon: 'article',
                hasDropdown: true,
                children: [
                    {
                        title: 'Создать документ',
                        url: '/documents',
                        isAdmin: true
                    },
                    {
                        title: 'Список документов',
                        url: '/documents/list',
                        isAdmin: true
                    },
                    {
                        title: 'Прайс-лист',
                        url: '/documents/price/list'
                    }
                ]
            },
            {
                title: 'Склад',
                url: '#',
                icon: 'work',
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
                    {
                        title: 'Заканчивающиеся товары',
                        url: '/products/stock/out',
                        isAdmin: true
                    }
                ],
            },
            {
                title: 'Продавцы',
                url: '#',
                hasDropdown: true,
                icon: 'persons',
                children: [
                    {
                        title: 'Задания',
                        url: '/tasks/index',
                    },
                    {
                        title: 'Обучение',
                        url: '/education/index'
                    }
                ],
            },
            {
                title: 'Статистика',
                icon: 'analytics',
                url: '#',
                hasDropdown: true,
                children: [
                    {
                        title: 'Баланс товаров',
                        url: '/products/balance'
                    },
                    {
                        title: 'Клиенты',
                        url: '/analytics/clients'
                    },
                    {
                        title: 'Бренды',
                        url: '/analytics/brands'
                    }
                ]
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
                    },
                    {
                        title: 'Аналитика',
                        url: '/kaspi/analytics'
                    }
                ]
            },
            {
                title: 'Партнеры',
                url: '#',
                isAdmin: true,
                hasDropdown: true,
                icon: 'supervised_user_circle',
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
                title: 'Интернет-магазин',
                url: '/shop/orders',
                icon: 'store'
            },
            {
                title: 'Обучение',
                url: '/education/index',
                icon: 'grading'
            },
            {
                title: 'Промокоды',
                url: '/promocode',
                icon: 'receipt'
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
