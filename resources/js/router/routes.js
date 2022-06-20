import Dashboard from "@/views/Dashboard/Dashboard";
import Users from "@/views/Users/Users";
import Stores from "@/views/Stores/Stores";
import Control from "@/views/Control/Control";
import Clients from "@/views/Clients/Clients";
import Transfers from "@/views/Transfers/Transfers";
import Hits from '@/views/Hits/Hits';
import Login from "@/views/Login/Login";
import Goals from "@/views/Goals/Goals";
import Sportsmen from "@/views/Sportsmen/Sportsmen";
import Plan from "@/views/Plan/Plan";
import MVPProducts from "@/views/MVPProducts/MVPProducts";
import Rating from "@/views/Rating/Rating";
import Revision from "@/views/Revision/Revision";
import Arrivals from "@/views/Arrivals/Arrivals";
import ObserverPage from "@/views/ObserverPage/ObserverPage";
import Promocodes from "@/views/Promocodes/Promocodes";
import PartnersStats from "@/views/PartnersStats/PartnersStats";
import ProductsV2 from '@/views/v2/Products/Products';
import ProductsV3 from '@/views/v3/Products/Products';
import CartV3 from '@/views/v3/Cart/Cart';
import CartPartner from '@/views/v3/Cart/CartPartner';
import Banner from "@/views/Banners/Banner";
import ReportsV3 from '@/views/v3/Reports/Reports';
import RelatedProducts from '@/views/v3/RelatedProducts/RelatedProducts';
import KaspiProducts from "@/views/Kaspi/KaspiProducts";
import KaspiOrders from "@/views/Kaspi/KaspiOrders";
import OrdersPage from "@/views/Orders/OrdersPage";
import ModeratorProducts from "@/views/Moderator/Products";
import NewsPage from "@/views/News/NewsPage";
import SupplierReports from "@/views/v3/Reports/SupplierReports";
import ProductReports from "@/views/v3/Reports/ProductReports";
import CompanionTransferIndex from "@/views/Companions/Transfers/Index";
import CompanionProducts from "@/views/Companions/Products/Index";
import CreateDocuments from "@/views/Documents/CreateDocuments";
import ProductBalance from "@/views/v3/Products/ProductBalance";
import AnalyticsClients from "@/views/Analytics/Clients";
import KaspiAnalytics from "@/views/Kaspi/KaspiAnalytics";
import DocumentsList from "@/views/Documents/DocumentsList";
import PriceList from "@/views/PriceList/PriceList";
import TasksIndex from "@/views/Sellers/Tasks/TasksIndex";
import EducationIndex from "@/views/Sellers/Education/EducationIndex";
import Brands from "@/views/Analytics/Brands";
import ProductsOutOfStock from "@/views/v3/Products/ProductsOutOfStock";
import PreordersIndex from "@/views/v3/Preorders/PreordersIndex";
import ShiftSettings from "@/views/Shifts/ShiftSettings";
import ShiftPenalty from "@/views/Shifts/ShiftPenalty";
import ShiftsList from "@/views/Shifts/ShiftsList";
import SaleAnalytics from "@/views/Analytics/SaleAnalytics";
import ArrivalAnalytics from "@/views/Analytics/ArrivalAnalytics";
import ProductEarning from "@/views/Economy/ProductEarning";
import CurrentArrivals from "@/components/Segments/Arrivals/CurrentArrivals";
import BookingIndex from "@/views/Booking/BookingIndex";
import BookingCart from "@/views/Booking/BookingCart";
import SiteFooter from "@/views/Site/SiteFooter";
import StocksIndex from "@/views/Stocks/StocksIndex";
import StocksCreate from "@/views/Stocks/StocksCreate";
import TrainerSales from "@/views/Analytics/TrainerSales";
import PartnerSales from "@/views/Analytics/PartnerSales";
import ClientView from "@/views/Clients/ClientView";
import ClientSalesDate from "@/views/Clients/ClientSalesDate";
import SaleAnalyticsByBrand from "@/views/Analytics/SaleAnalyticsByBrand";
import SaleAnalyticsBySellerAndProducts from "@/views/Analytics/SaleAnalyticsBySellerAndProducts";
import SalesSchedule from "@/views/Analytics/SalesSchedule";
import ProductMarginTypes from "@/views/Economy/ProductMarginTypes";
import TransferUpdate from "@/views/Transfers/TransferUpdate";
import ProductTags from "@/views/Moderator/ProductTags";
import SeoCategory from "@/views/SEO/SeoCategory";
import ProductSubcategories from "@/views/Moderator/ProductSubcategories";
import IherbIndexPage from "@/views/IHerb/IherbIndexPage";

const routes = [
    {
        path: '/',
        component: Dashboard,
    },
    {
        path: '/users',
        component: Users,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            },
        }
    },
    {
        path: '/stores',
        component: Stores,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            },
        }

    },
    {
        path: '/categories',
        component: Control,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            },
        }
    },
    {
        path: '/clients',
        component: Clients,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_SELLER: true,
                IS_SENIORSELLER: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true,
                IS_FRANCHISE: true,
            }
        }
    },
    {
        path: '/clients/:id',
        component: ClientView,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_SELLER: true,
                IS_SENIORSELLER: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true,
                IS_FRANCHISE: true
            }
        }
    },
    {
        path: '/analytics/clients/sales',
        component: ClientSalesDate,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/plan',
        component: Plan,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
            },
        }
    },
    {
        path: '/transfer',
        component: Transfers
    },
    {
        path: '/transfers/update/:id',
        component: TransferUpdate
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
        name: 'Login',
        component: Login,
        meta: {
            CAN_ENTER: {
                IS_GUEST: true
            },
        }
    },
    {
        path: '/stats/mvp_products',
        component: MVPProducts,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            },
        }
    },
    {
        path: '/shop/rating',
        component: Rating,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            },
        }
    },
    {
        path: '/shop/banners',
        component: Banner,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_MODERATOR: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            },
        }
    },
    {
        path: '/shop/orders',
        component: OrdersPage,
    },
    {
        path: '/shop/related',
        component: RelatedProducts,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true,
                IS_MODERATOR: true
            }
        }
    },
    {
        path: '/revision',
        component: Revision,
        meta: {
            CAN_ENTER: {
                IS_SELLER: true,
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_SENIORSELLER: true,
            }
        }
    },
    {
        path: '/arrivals',
        component: Arrivals,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_SELLER: true,
                IS_STOREKEEPER: true,
                IS_BOSS: true,
                IS_SENIORSELLER: true,
                IS_MARKETOLOG: true,
            },
        }
    },
    {
        path: '/observer',
        component: ObserverPage,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_OBSERVER: true,
                IS_BOSS: true,

            },
        }
    },
    {
        path: '/promocode',
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_SELLER: true,
                IS_BOSS: true,
                IS_SENIORSELLER: true,
                IS_MARKETOLOG: true,
                IS_FRANCHISE: true
            },
        },
        component: Promocodes
    },
    {
        path: '/stats/partners',
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            },
        },
        component: PartnersStats
    },
    {
        path: '/v2/products',
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            },
        },
        component: ProductsV2
    },
    {
        path: '/products',
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_PARTNER_SELLERS: true,
                IS_STOREKEEPER: true,
                IS_BOSS: true,
                IS_SENIORSELLER: true,
                IS_SELLER: true,
                IS_MODERATOR: true,
                IS_FRANCHISE: true,
            },
        },
        component: ProductsV3
    },
    {
        path: '/products/balance',
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_OBSERVER: true,
                IS_FRANCHISE: true
            }
        },
        component: ProductBalance
    },
    {
        path: '/cart',
        component: CartV3,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_SELLER: true,
                IS_BOSS: true,
                IS_SENIORSELLER: true,
                IS_FRANCHISE: true
            }
        }
    },
    {
        path: '/cart/partner',
        component: CartPartner,
        meta: {
            CAN_ENTER: {
                IS_PARTNER_SELLERS: true,
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/reports',
        component: ReportsV3
    },
    {
        path: '/kaspi/products',
        component: KaspiProducts,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            },
        },
    },
    {
        path: '/kaspi/orders',
        component: KaspiOrders,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/moderator/products',
        component: ModeratorProducts,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/moderator/news',
        component: NewsPage,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/reports/products',
        component: ProductReports,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/supplier/reports',
        component: SupplierReports,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_SUPPLIER: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/companions/transfer',
        component: CompanionTransferIndex,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_PARTNER_SELLERS: true,
                IS_BOSS: true,
            }
        }
    },
    {
        path: '/companion/products',
        component: CompanionProducts,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_PARTNER_SELLERS: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/documents',
        component: CreateDocuments,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/documents/list',
        component: DocumentsList,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            }
        }
    },
    {
        path: '/documents/price/list',
        component: PriceList,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            }
        }
    },
    {
        path: '/analytics/clients',
        component: AnalyticsClients,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/analytics/brands',
        component: Brands,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/analytics/sales/brands',
        component: SaleAnalyticsByBrand,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/analytics/sales/brands/sellers',
        component: SaleAnalyticsBySellerAndProducts,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/analytics/sales/schedule',
        component: SalesSchedule,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/kaspi/analytics',
        component: KaspiAnalytics,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true
            }
        }
    },
    {
        path: '/tasks/index',
        component: TasksIndex,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/education/index',
        component: EducationIndex,
    },
    {
        path: '/products/stock/out',
        component: ProductsOutOfStock,
        meta: {
            CAN_ENTER: {
                IS_ADMIN: true,
                IS_BOSS: true,
            }
        }
    },
    {
        path: '/preorders/index',
        component: PreordersIndex
    },
    {
        path: '/shifts/settings',
        component: ShiftSettings
    },
    {
        path: '/shifts/penalty',
        component: ShiftPenalty
    },
    {
        path: '/shifts/index',
        component: ShiftsList
    },
    {
        path: '/analytics/sales',
        component: SaleAnalytics
    },
    {
        path: '/analytics/arrivals',
        component: ArrivalAnalytics
    },
    {
        path: '/economy/seller/earnings',
        component: ProductEarning,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true
            }
        }
    },
    {
        path: '/economy/margin/types',
        component: ProductMarginTypes,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
                IS_MARKETOLOG: true,
            }
        }
    },
    {
        path: '/products/tags',
        component: ProductTags,
    },
    {
        path: '/booking',
        component: BookingIndex,
    },
    {
        path: '/booking/create',
        component: CurrentArrivals,
    },
    {
        path: '/booking/:id',
        component: BookingCart,
    },
    {
        path: '/site/footer',
        component: SiteFooter,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
                IS_ADMIN: true,
                IS_MODERATOR: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/stocks/index',
        component: StocksIndex,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
                IS_ADMIN: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/stocks/create',
        component: StocksCreate,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
                IS_ADMIN: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/analytics/trainer/rating',
        component: TrainerSales,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
                IS_ADMIN: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/analytics/partners/rating',
        component: PartnerSales,
        meta: {
            CAN_ENTER: {
                IS_BOSS: true,
                IS_ADMIN: true,
                IS_MARKETOLOG: true
            }
        }
    },
    {
        path: '/seo/category',
        component: SeoCategory
    },
    {
        path: '/products/subcategories',
        component: ProductSubcategories
    },
    {
        path: '/products/iherb',
        component: IherbIndexPage
    }
];

export default routes;
