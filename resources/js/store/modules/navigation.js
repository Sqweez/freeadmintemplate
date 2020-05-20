const navigationModule = {
    state: {
        menu: [
            {
                title: 'Главная страница',
                url: '/',
                icon: 'dashboard'
            },
            {
                title: 'Продавцы',
                url: '/users',
                icon: 'person',
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
                        url: '/stores'
                    },
                    {
                        title: 'Все товары',
                        url: '/products'
                    },
                    {
                        title: 'Категории',
                        url: '/categories'
                    },
                    {
                        title: 'Корзина',
                        url: '/cart'
                    },
                    {
                        title: 'Перемещения',
                        url: '/transfer'
                    },

                ],
            },
            {
                title: 'Отчеты по продажам',
                url: '/reports',
                icon: 'report'
            },
            {
                title: 'Интернет-магазин',
                url: '#',
                icon: 'home',
                hasDropdown: true,
                children: [
                    {
                        title: 'Товары магазин',
                        url: '/shop/products'
                    }
                ]
            }
        ],
    },
    getters: {
        navigations: state => state.menu,
    }
};

export default navigationModule;
