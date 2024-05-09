const navigationModule = {
    state: {
        bossMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Пользователи',
                url: '/users',
                icon: 'person',
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person',
            },
            {
                title: 'Оптовые клиенты',
                url: '/clients/wholesale',
                icon: 'person',
            },
            {
                title: 'Изъятия',
                url: '/with-drawal',
                icon: 'report',
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
                    },
                    {
                        title: 'Список документов',
                        url: '/documents/list',
                    },
                    {
                        title: 'Прайс-лист',
                        url: '/documents/price/list',
                    },
                ],
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
                    },
                    {
                        title: 'Категории',
                        url: '/categories',
                    },
                    {
                        title: 'Товары',
                        url: '/products',
                    },
                    {
                        title: 'Корзина',
                        url: '/cart',
                    },
                    {
                        title: 'Бронирование товара',
                        url: '/booking',
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
                    },
                    {
                        title: 'Поступления',
                        url: '/arrivals',
                    },
                    {
                        title: 'Заканчивающиеся товары',
                        url: '/products/stock/out',
                    },
                    {
                        title: 'Предзаказы',
                        url: '/preorders/index',
                    },
                    {
                        title: 'Сроки годности',
                        url: '/products/best-before',
                    },
                    {
                        title: 'Товарные матрицы',
                        url: '/matrixes',
                    },
                    {
                        title: 'Ревизии',
                        url: '/revision',
                    },
                ],
            },
            {
                title: 'IHerb',
                url: '#',
                icon: 'work',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/products/iherb',
                    },
                    {
                        title: 'Прайс-лист',
                        url: '/products/iherb/price',
                    },
                    {
                        title: 'Массовый редактор',
                        url: '/iherb/mass',
                    },
                ],
            },
            {
                title: 'Рабочий график',
                url: '/working-schedule/index',
                icon: 'view_timeline',
            },
            {
                title: 'Экономика',
                url: '#',
                hasDropdown: true,
                icon: 'account_balance_wallet',
                children: [
                    {
                        title: 'Список смен',
                        url: '/shifts/index',
                    },
                    {
                        title: 'Настройки смен',
                        url: '/shifts/settings',
                    },
                    {
                        title: 'Штрафы/Вознаграждения',
                        url: '/shifts/penalty',
                    },
                    {
                        title: 'План продаж',
                        url: '/plan',
                    },
                    {
                        title: 'Проценты от продаж',
                        url: '/economy/seller/earnings',
                    },
                    {
                        title: 'Типы маржинальности',
                        url: '/economy/margin/types',
                    },
                    {
                        title: 'Юридические лица',
                        url: '/legal-entity',
                    },
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
                        url: '/education/index',
                    },
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
                        url: '/products/balance',
                    },
                    {
                        title: 'Клиенты',
                        url: '/analytics/clients',
                    },
                    {
                        title: 'Бренды',
                        url: '/analytics/brands',
                    },
                    {
                        title: 'Аналитика продаж',
                        url: '/analytics/sales',
                    },
                    {
                        title: 'Аналитика поступлений',
                        url: '/analytics/arrivals',
                    },
                    {
                        title: 'Аналитика продаж бренды',
                        url: '/analytics/sales/brands',
                    },
                    {
                        url: '/analytics/sales/brands/sellers',
                        title: 'Аналитика продаж продавцы',
                    },
                    {
                        url: '/analytics/sales/schedule',
                        title: 'График продаж',
                    },
                ],
            },
            {
                title: 'Настройки',
                url: '/settings',
                icon: 'settings',
            },
            {
                title: 'Отчеты',
                url: '#',
                icon: 'report',
                hasDropdown: true,
                children: [
                    {
                        title: 'Отчеты по продажам',
                        url: '/reports',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по товарам',
                        url: '/report/products',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по тренерам',
                        url: '/analytics/trainer/rating',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по партнерам',
                        url: '/analytics/partners/rating',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по клиентам',
                        url: '/analytics/clients/sales',
                        icon: 'report',
                    },
                ],
            },
            {
                title: 'Kaspi',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/kaspi/products',
                    },
                    {
                        title: 'Заказы',
                        url: '/kaspi/orders',
                    },
                    {
                        title: 'Аналитика',
                        url: '/kaspi/analytics',
                    },
                ],
            },
            {
                title: 'Партнеры',
                url: '#',
                hasDropdown: true,
                icon: 'supervised_user_circle',
                children: [
                    {
                        title: 'Закупы',
                        url: '/companions/transfer',
                    },
                ],
            },
            {
                title: 'Модератор',
                url: '#',
                icon: 'dashboard',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/moderator/products',
                    },
                    {
                        title: 'Новости',
                        url: '/moderator/news',
                    },
                    {
                        title: 'Теги',
                        url: '/products/tags',
                    },
                    {
                        title: 'SEO-категории',
                        url: '/seo/category',
                    },
                    {
                        title: 'Доп категории',
                        url: '/products/subcategories',
                    },
                ],
            },
            {
                title: 'Интернет-магазин',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Акции',
                        url: '/stocks/index',
                    },
                    {
                        title: 'Цели',
                        url: '/shop/goals',
                    },
                    {
                        title: 'Атлеты',
                        url: '/shop/sportsmen',
                    },
                    {
                        title: 'Рейтинг продавцов',
                        url: '/shop/rating',
                    },
                    {
                        title: 'Промокоды',
                        url: '/promocode',
                    },
                    {
                        title: 'Баннеры',
                        url: '/shop/banners',
                    },
                    {
                        title: 'Связанные товары',
                        url: '/shop/related',
                    },
                    {
                        title: 'Заказы',
                        url: '/shop/orders',
                    },
                    {
                        title: 'Футер',
                        url: '/site/footer',
                    },
                ],
            },
        ],
        partner_sellersMenu: [
            {
                title: 'Главная',
                icon: 'dashboard',
                url: '/',
            },
            {
                title: 'Корзина',
                icon: 'store',
                url: '/cart/partner',
            },
            {
                title: 'Товары',
                icon: 'dashboard',
                url: '/companion/products',
            },
        ],
        opt_managerMenu: [
            {
                title: 'Товары',
                icon: 'dashboard',
                url: '/opt/products',
            },
            {
                title: 'Клиенты',
                icon: 'dashboard',
                url: '/opt/clients',
            },
            {
                title: 'Заказы',
                icon: 'dashboard',
                url: '/opt/orders',
            },
            {
                title: 'Товары дня',
                icon: 'dashboard',
                url: '/opt/daily-deals',
            },
        ],
        moderatorMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Товары',
                url: '/moderator/products',
                icon: 'dashboard',
            },
            {
                title: 'Баннеры',
                url: '/shop/banners',
                icon: 'dashboard',
            },
            {
                icon: 'dashboard',
                title: 'Теги',
                url: '/products/tags',
            },
            {
                icon: 'dashboard',
                title: 'SEO-категории',
                url: '/seo/category',
            },
            {
                icon: 'dashboard',
                title: 'Доп категории',
                url: '/products/subcategories',
            },
        ],
        supplierMenu: [
            {
                title: 'Отчеты по продажам',
                url: '/supplier/reports',
                icon: 'dashboard',
            },
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
                icon: 'dashboard',
            },
            {
                title: 'Корзина',
                url: '/cart',
                icon: 'report',
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report',
            },
            {
                title: 'Ревизии',
                url: '/revision',
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
                isAdmin: true,
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person',
            },
            {
                title: 'Оптовые клиенты',
                url: '/clients/wholesale',
                icon: 'person',
            },
            {
                title: 'Изъятия',
                url: '/with-drawal',
                icon: 'report',
            },
            {
                title: 'Рабочий график',
                url: '/working-schedule/index',
                icon: 'view_timeline',
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
                        isAdmin: true,
                    },
                    {
                        title: 'Список документов',
                        url: '/documents/list',
                        isAdmin: true,
                    },
                    {
                        title: 'Прайс-лист',
                        url: '/documents/price/list',
                    },
                ],
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
                        isAdmin: true,
                    },
                    {
                        title: 'Категории',
                        url: '/categories',
                        isAdmin: true,
                    },
                    {
                        title: 'Товары',
                        url: '/products',
                    },
                    {
                        title: 'Корзина',
                        url: '/cart',
                    },
                    {
                        title: 'Бронирование товара',
                        url: '/booking',
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
                        isAdmin: true,
                    },
                    {
                        title: 'Предзаказы',
                        url: '/preorders/index',
                    },
                    {
                        title: 'Сроки годности',
                        url: '/products/best-before',
                    },
                    {
                        title: 'Товарные матрицы',
                        url: '/matrixes',
                    },
                    {
                        title: 'Ревизии',
                        url: '/revision',
                    },
                ],
            },
            {
                title: 'IHerb',
                url: '#',
                icon: 'work',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/products/iherb',
                    },
                    {
                        title: 'Прайс-лист',
                        url: '/products/iherb/price',
                    },
                    {
                        title: 'Массовый редактор',
                        url: '/iherb/mass',
                    },
                ],
            },
            {
                title: 'Экономика',
                url: '#',
                hasDropdown: true,
                icon: 'account_balance_wallet',
                children: [
                    {
                        title: 'Список смен',
                        url: '/shifts/index',
                    },
                    {
                        title: 'Типы маржинальности',
                        url: '/economy/margin/types',
                    },
                    {
                        title: 'Юридические лица',
                        url: '/legal-entity',
                    },
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
                        url: '/education/index',
                    },
                ],
            },
            {
                title: 'Настройки',
                url: '/settings',
                icon: 'settings',
            },
            {
                title: 'Отчеты',
                url: '#',
                icon: 'report',
                hasDropdown: true,
                children: [
                    {
                        title: 'Отчеты по продажам',
                        url: '/reports',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по товарам',
                        url: '/report/products',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по тренерам',
                        url: '/analytics/trainer/rating',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по партнерам',
                        url: '/analytics/partners/rating',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по клиентам',
                        url: '/analytics/clients/sales',
                        icon: 'report',
                    },
                ],
            },
            {
                title: 'Аналитика продаж бренды',
                url: '/analytics/sales/brands',
            },
            {
                title: 'Аналитика продаж продавцы',
                url: '/analytics/sales/brands/sellers',
            },
            {
                url: '/analytics/sales/schedule',
                title: 'График продаж',
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
                        url: '/kaspi/products',
                    },
                    {
                        title: 'Заказы',
                        url: '/kaspi/orders',
                    },
                    {
                        title: 'Аналитика',
                        url: '/kaspi/analytics',
                    },
                ],
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
                        url: '/companions/transfer',
                    },
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
                        url: '/moderator/products',
                    },
                    {
                        title: 'Новости',
                        url: '/moderator/news',
                    },
                    {
                        icon: 'dashboard',
                        title: 'Теги',
                        url: '/products/tags',
                    },
                    {
                        title: 'SEO-категории',
                        url: '/seo/category',
                    },
                    {
                        title: 'Доп категории',
                        url: '/products/subcategories',
                    },
                ],
            },
            {
                title: 'Интернет-магазин',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                isAdmin: true,
                children: [
                    {
                        title: 'Акции',
                        url: '/stocks/index',
                    },
                    {
                        title: 'Цели',
                        url: '/shop/goals',
                    },
                    {
                        title: 'Атлеты',
                        url: '/shop/sportsmen',
                    },
                    {
                        title: 'Рейтинг продавцов',
                        url: '/shop/rating',
                    },
                    {
                        title: 'Промокоды',
                        url: '/promocode',
                    },
                    {
                        title: 'Баннеры',
                        url: '/shop/banners',
                    },
                    {
                        title: 'Связанные товары',
                        url: '/shop/related',
                    },
                    {
                        title: 'Заказы',
                        url: '/shop/orders',
                    },
                    {
                        title: 'Футер',
                        url: '/site/footer',
                    },
                ],
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
        seniorSellerMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person',
            },
            {
                title: 'Изъятия',
                url: '/with-drawal',
                icon: 'report',
            },
            {
                title: 'Склад',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/products',
                    },
                    {
                        title: 'Корзина',
                        url: '/cart',
                    },
                    {
                        title: 'Бронирование товара',
                        url: '/booking',
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
                    },
                    {
                        title: 'Предзаказы',
                        url: '/preorders/index',
                    },
                    {
                        title: 'Поступления',
                        url: '/arrivals',
                    },
                    {
                        title: 'Сроки годности',
                        url: '/products/best-before',
                    },
                    {
                        title: 'Ревизии',
                        url: '/revision',
                    },
                ],
            },
            {
                title: 'Рабочий график',
                url: '/working-schedule/index',
                icon: 'view_timeline',
            },
            {
                title: 'Настройки',
                url: '/settings',
                icon: 'settings',
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report',
            },
            {
                title: 'Интернет-магазин',
                url: '/shop/orders',
                icon: 'store',
            },
            {
                title: 'Обучение',
                url: '/education/index',
                icon: 'grading',
            },
            {
                title: 'Промокоды',
                url: '/promocode',
                icon: 'receipt',
            },
        ],
        sellerMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Изъятия',
                url: '/with-drawal',
                icon: 'report',
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person',
            },
            {
                title: 'Рабочий график',
                url: '/working-schedule/index',
                icon: 'view_timeline',
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
                    },
                    /*{
                        title: 'Список документов',
                        url: '/documents/list',
                    },
                    {
                        title: 'Прайс-лист',
                        url: '/documents/price/list'
                    }*/
                ],
            },
            {
                title: 'Склад',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары',
                        url: '/products',
                    },
                    {
                        title: 'Корзина',
                        url: '/cart',
                    },
                    {
                        title: 'Бронирование товара',
                        url: '/booking',
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer',
                    },
                    {
                        title: 'Предзаказы',
                        url: '/preorders/index',
                    },
                    {
                        title: 'Поступления',
                        url: '/arrivals',
                    },
                    {
                        title: 'Сроки годности',
                        url: '/products/best-before',
                    },
                    {
                        title: 'Ревизии',
                        url: '/revision',
                    },
                ],
            },
            {
                title: 'Настройки',
                url: '/settings',
                icon: 'settings',
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report',
            },
            {
                title: 'Интернет-магазин',
                url: '/shop/orders',
                icon: 'store',
            },
            {
                title: 'Обучение',
                url: '/education/index',
                icon: 'grading',
            },
            {
                title: 'Промокоды',
                url: '/promocode',
                icon: 'receipt',
            },
        ],
        observerMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Баланс товаров',
                url: '/products/balance',
                icon: 'analytics',
            },
        ],
        marketologMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person',
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
                        url: '/education/index',
                    },
                ],
            },
            {
                title: 'Статистика',
                icon: 'analytics',
                url: '#',
                hasDropdown: true,
                children: [
                    {
                        title: 'Аналитика продаж',
                        url: '/analytics/sales',
                    },
                    {
                        title: 'Аналитика поступлений',
                        url: '/analytics/arrivals',
                    },
                    {
                        title: 'Аналитика продаж бренды',
                        url: '/analytics/sales/brands',
                    },
                    {
                        url: '/analytics/sales/schedule',
                        title: 'График продаж',
                    },
                    {
                        url: '/analytics/sales/brands/sellers',
                        title: 'Аналитика продаж продавцы',
                    },
                ],
            },
            {
                title: 'Экономика',
                url: '#',
                hasDropdown: true,
                icon: 'account_balance_wallet',
                children: [
                    {
                        title: 'Список смен',
                        url: '/shifts/index',
                    },
                    {
                        title: 'Настройки смен',
                        url: '/shifts/settings',
                    },
                    {
                        title: 'Штрафы/Вознаграждения',
                        url: '/shifts/penalty',
                    },
                    {
                        title: 'План продаж',
                        url: '/plan',
                    },
                    {
                        title: 'Проценты от продаж',
                        url: '/economy/seller/earnings',
                    },
                    {
                        title: 'Типы маржинальности',
                        url: '/economy/margin/types',
                    },
                ],
            },
            {
                title: 'Отчеты',
                url: '#',
                icon: 'report',
                hasDropdown: true,
                children: [
                    {
                        title: 'Отчеты по продажам',
                        url: '/reports',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по товарам',
                        url: '/report/products',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по тренерам',
                        url: '/analytics/trainer/rating',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по партнерам',
                        url: '/analytics/partners/rating',
                        icon: 'report',
                    },
                    {
                        title: 'Отчеты по клиентам',
                        url: '/analytics/clients/sales',
                        icon: 'report',
                    },
                ],
            },
            {
                title: 'Типы маржинальности',
                url: '/economy/margin/types',
                icon: 'article',
            },
            {
                title: 'Поступления',
                url: '/arrivals',
                icon: 'moped',
            },
            {
                title: 'Перемещения',
                url: '/transfer',
                icon: 'moped',
            },
            {
                icon: 'dashboard',
                title: 'Теги',
                url: '/products/tags',
            },
            {
                icon: 'dashboard',
                title: 'Доп категории',
                url: '/products/subcategories',
            },
            {
                title: 'Интернет-магазин',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Акции',
                        url: '/stocks/index',
                    },
                    {
                        title: 'Цели',
                        url: '/shop/goals',
                    },
                    {
                        title: 'Атлеты',
                        url: '/shop/sportsmen',
                    },
                    {
                        title: 'Рейтинг продавцов',
                        url: '/shop/rating',
                    },
                    {
                        title: 'Промокоды',
                        url: '/promocode',
                    },
                    {
                        title: 'Баннеры',
                        url: '/shop/banners',
                    },
                    {
                        title: 'Связанные товары',
                        url: '/shop/related',
                    },
                    {
                        title: 'Футер',
                        url: '/site/footer',
                    },
                ],
            },
        ],
        franchiseMenu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard',
            },
            {
                title: 'Пользователи',
                url: '/users',
                icon: 'person',
                isAdmin: true,
            },
            {
                title: 'Изъятия',
                url: '/with-drawal',
                icon: 'report',
            },
            {
                title: 'Клиенты',
                url: '/clients',
                icon: 'person',
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
                        isAdmin: true,
                    },
                    {
                        title: 'Категории',
                        url: '/categories',
                        isAdmin: true,
                    },
                    {
                        title: 'Товары',
                        url: '/products',
                    },
                    {
                        title: 'Корзина',
                        url: '/cart',
                    },
                    {
                        title: 'Бронирование товара',
                        url: '/booking',
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
                        isAdmin: true,
                    },
                    {
                        title: 'Предзаказы',
                        url: '/preorders/index',
                    },
                    {
                        title: 'Сроки годности',
                        url: '/products/best-before',
                    },
                    {
                        title: 'Ревизии',
                        url: '/revision',
                    },
                ],
            },
            {
                title: 'Настройки',
                url: '/settings',
                icon: 'settings',
            },
            {
                title: 'Список смен',
                url: '/shifts/index',
                icon: 'report',
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report',
            },
            {
                title: 'Баланс товаров',
                url: '/products/balance',
                icon: 'report',
            },
            {
                title: 'Интернет-магазин',
                url: '/shop/orders',
                icon: 'report',
            },
            {
                title: 'Промокоды',
                url: '/promocode',
                icon: 'report',
            },
        ],
    },
    getters: {
        navigations: (state, getters) => {
            const ROLE = getters.CURRENT_ROLE;
            return state[`${ROLE}Menu`];
        },
    },
};

export default navigationModule;
