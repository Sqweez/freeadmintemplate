<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\AnalyticSearch
 *
 * @property int $id
 * @property string|null $search
 * @property int $client_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $client
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AnalyticSearch extends \Eloquent {}
}

namespace App{
/**
 * App\Arrival
 *
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property int $is_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $_products
 * @property-read int|null $_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ArrivalProducts[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival query()
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereUserId($value)
 * @mixin \Eloquent
 * @property string $comment
 * @property string|null $arrived_at
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereArrivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereComment($value)
 */
	class Arrival extends \Eloquent {}
}

namespace App{
/**
 * App\ArrivalProducts
 *
 * @property int $id
 * @property int $product_id
 * @property int $arrival_id
 * @property int $count
 * @property int $purchase_price
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts wherePurchasePrice($value)
 * @mixin \Eloquent
 */
	class ArrivalProducts extends \Eloquent {}
}

namespace App{
/**
 * App\Attribute
 *
 * @property int $id
 * @property string $attribute_name
 * @property-read \App\AttributeProduct|null $value
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereAttributeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereId($value)
 * @mixin \Eloquent
 */
	class Attribute extends \Eloquent {}
}

namespace App{
/**
 * App\AttributeProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property string $attribute_value
 * @property-read \App\Attribute $attribute_name
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereAttributeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereProductId($value)
 * @mixin \Eloquent
 */
	class AttributeProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Banner
 *
 * @property int $id
 * @property string $image
 * @property int $is_active
 * @property string|null $description
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $mobile_image
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereMobileImage($value)
 */
	class Banner extends \Eloquent {}
}

namespace App{
/**
 * App\Cart
 *
 * @property int $id
 * @property string $user_token
 * @property string $type
 * @property int $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CartProduct[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart ofUser($token)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserToken($value)
 * @mixin \Eloquent
 */
	class Cart extends \Eloquent {}
}

namespace App{
/**
 * App\CartProduct
 *
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct cart($cart_id)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct store($store_id)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct ofProduct($product_id)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct product($product_id)
 */
	class CartProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Category
 *
 * @property int $id
 * @property string $category_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $category_img
 * @property string $category_slug
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Subcategory[] $subcategories
 * @property-read int|null $subcategories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category ofSlug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategoryImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategorySlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CategoryProduct[] $category_product
 * @property-read int|null $category_product_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\RelatedProduct[] $relatedProducts
 * @property-read int|null $related_products_count
 * @property int $is_site_visible
 * @method static \Illuminate\Database\Eloquent\Builder|Category site()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsSiteVisible($value)
 */
	class Category extends \Eloquent {}
}

namespace App{
/**
 * App\CategoryProduct
 *
 * @property int $product_id
 * @property int $category_id
 * @property-read \App\Category $category
 * @property-read \App\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct whereProductId($value)
 * @mixin \Eloquent
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct whereId($value)
 */
	class CategoryProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Client
 *
 * @property int $id
 * @property string $client_name
 * @property string $client_phone
 * @property string $client_card
 * @property int $client_discount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $password
 * @property string $address
 * @property string $user_token
 * @property string $email
 * @property int $is_partner
 * @property int $client_city
 * @property string|null $partner_expired_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Store $city
 * @property-read mixed $balance
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Sale[] $partner_sales
 * @property-read int|null $partner_sales_count
 * @property-read Collection|Sale[] $purchases
 * @property-read int|null $purchases_count
 * @property-read Collection|ClientSale[] $sales
 * @property-read int|null $sales_count
 * @property-read Collection|ClientTransaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client ofPhone($phone)
 * @method static \Illuminate\Database\Eloquent\Builder|Client ofToken($token)
 * @method static \Illuminate\Database\Query\Builder|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client partner()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIsPartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePartnerExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUserToken($value)
 * @method static \Illuminate\Database\Query\Builder|Client withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Client withoutTrashed()
 * @mixin \Eloquent
 * @property-read Collection|\App\Promocode[] $promocodes
 * @property-read int|null $promocodes_count
 * @property int $loyalty_id
 * @property string $photo
 * @property string $job
 * @property string $instagram
 * @property-read \App\v2\Models\Loyalty $loyalty
 * @method static \Illuminate\Database\Eloquent\Builder|Client platinumClients()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLoyaltyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePhoto($value)
 */
	class Client extends \Eloquent {}
}

namespace App{
/**
 * App\ClientSale
 *
 * @property int $id
 * @property int $client_id
 * @property int $sale_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Sale $sale
 */
	class ClientSale extends \Eloquent {}
}

namespace App{
/**
 * App\ClientTransaction
 *
 * @property int $id
 * @property int $amount
 * @property int $user_id
 * @property int $client_id
 * @property int $sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereUserId($value)
 * @mixin \Eloquent
 */
	class ClientTransaction extends \Eloquent {}
}

namespace App{
/**
 * App\CompanionSale
 *
 * @property int $id
 * @property int $store_id
 * @property int $companion_id
 * @property int $user_id
 * @property int $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_consignment
 * @property int $transfer_id
 * @property-read \App\Store $companion
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereCompanionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereIsConsignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereUserId($value)
 * @mixin \Eloquent
 */
	class CompanionSale extends \Eloquent {}
}

namespace App{
/**
 * App\CompanionSaleProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $companion_sale_id
 * @property int $purchase_price
 * @property int $product_price
 * @property int $discount
 * @property-read \App\v2\Models\ProductSku $product
 * @property-read \App\CompanionSale $sale
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereCompanionSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct wherePurchasePrice($value)
 * @mixin \Eloquent
 */
	class CompanionSaleProduct extends \Eloquent {}
}

namespace App{
/**
 * App\CompanionTransaction
 *
 * @property int $id
 * @property int $transaction_sum
 * @property int $companion_id
 * @property int $user_id
 * @property int $companion_sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type
 * @property-read \App\Store $companion
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereCompanionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereCompanionSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereTransactionSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereUserId($value)
 * @mixin \Eloquent
 */
	class CompanionTransaction extends \Eloquent {}
}

namespace App{
/**
 * App\Document
 *
 * @property int $id
 * @property string $document
 * @property int $document_type
 * @property int $document_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $date
 * @property-read mixed $type
 */
	class Document extends \Eloquent {}
}

namespace App{
/**
 * App\Goal
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GoalPart[] $parts
 * @property-read int|null $parts_count
 * @method static \Illuminate\Database\Eloquent\Builder|Goal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $mobile_image
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereMobileImage($value)
 */
	class Goal extends \Eloquent {}
}

namespace App{
/**
 * App\GoalPart
 *
 * @property int $id
 * @property int $goal_id
 * @property int $category_id
 * @property int|null $subcategory_id
 * @property string $name
 * @property string|null $description
 * @property-read \App\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GoalPartProducts[] $products
 * @property-read int|null $products_count
 * @property-read \App\Subcategory|null $subcategory
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereGoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereSubcategoryId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereProducts($value)
 */
	class GoalPart extends \Eloquent {}
}

namespace App{
/**
 * App\GoalPartProducts
 *
 * @property int $id
 * @property int $goal_part_id
 * @property int $product_id
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts whereGoalPartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts whereProductId($value)
 * @mixin \Eloquent
 * @property-read \App\v2\Models\ProductSku $product
 */
	class GoalPartProducts extends \Eloquent {}
}

namespace App{
/**
 * App\Manufacturer
 *
 * @property int $id
 * @property string $manufacturer_name
 * @property string $manufacturer_img
 * @property string|null $manufacturer_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereManufacturerDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereManufacturerImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereManufacturerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read int|null $products_count
 */
	class Manufacturer extends \Eloquent {}
}

namespace App{
/**
 * App\ManufacturerProducts
 *
 * @property int $id
 * @property int $product_id
 * @property int $manufacturer_id
 * @property-read \App\Manufacturer|null $manufacturer
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts whereManufacturerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts whereProductId($value)
 * @mixin \Eloquent
 */
	class ManufacturerProducts extends \Eloquent {}
}

namespace App{
/**
 * App\NewsProduct
 *
 * @property int $id
 * @property int $news_id
 * @property int $product_id
 * @property-read \App\v2\Models\News $news
 * @property-read \App\v2\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct whereNewsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct whereProductId($value)
 * @mixin \Eloquent
 */
	class NewsProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Order
 *
 * @property int $id
 * @property string $user_token
 * @property int $payment
 * @property int $delivery
 * @property string $fullname
 * @property string $address
 * @property string $phone
 * @property string $city
 * @property string|null $email
 * @property string $store_id
 * @property string|null $comment
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $client_id
 * @property int $discount
 * @property int|null $balance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OrderProduct[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Store $store
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\City $city_text
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserToken($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_paid
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsPaid($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @property-read Model|\Eloquent $image
 * @property-read int|null $image_count
 */
	class Order extends \Eloquent {}
}

namespace App{
/**
 * App\OrderProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $order_id
 * @property int $purchase_price
 * @property int $product_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\ProductBatch $batch
 */
	class OrderProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Plan
 *
 * @property int $id
 * @property int $store_id
 * @property int $month_plan
 * @property int $week_plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereMonthPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereWeekPlan($value)
 * @mixin \Eloquent
 * @property int $prize
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePrize($value)
 */
	class Plan extends \Eloquent {}
}

namespace App{
/**
 * App\Price
 *
 * @property int $id
 * @property int $price
 * @property int $product_id
 * @property int $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Price extends \Eloquent {}
}

namespace App{
/**
 * App\Product
 *
 * @property int $id
 * @property string $product_name
 * @property string|null $product_description
 * @property int $product_price
 * @property string $product_barcode
 * @property int|null $group_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_hit
 * @property int $is_site_visible
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AttributeProduct[] $attributes
 * @property-read int|null $attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $children
 * @property-read int|null $children_count
 * @property-read mixed $current_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manufacturer[] $manufacturer
 * @property-read int|null $manufacturer_count
 * @property-read Product|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $price
 * @property-read int|null $price_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductImage[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductQuantity[] $product_quantity
 * @property-read int|null $product_quantity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductThumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Subcategory[] $subcategories
 * @property-read int|null $subcategories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tag
 * @property-read int|null $tag_count
 * @method static Builder|Product inStock($store_id = 1)
 * @method static Builder|Product isHit($param = 'false')
 * @method static Builder|Product main()
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product ofBrand($param)
 * @method static Builder|Product ofCategory($param)
 * @method static Builder|Product ofPrice($param)
 * @method static Builder|Product ofSearch($search)
 * @method static Builder|Product ofSubcategory($param)
 * @method static Builder|Product ofTag($search)
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereGroupId($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsHit($value)
 * @method static Builder|Product whereIsSiteVisible($value)
 * @method static Builder|Product whereProductBarcode($value)
 * @method static Builder|Product whereProductDescription($value)
 * @method static Builder|Product whereProductName($value)
 * @method static Builder|Product whereProductPrice($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $category_id
 * @property int|null $manufacturer_id
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereManufacturerId($value)
 * @property int $product_discount_price
 * @property int $grouping_attribute_id
 * @property int $subcategory_id
 * @method static Builder|Product whereGroupingAttributeId($value)
 * @method static Builder|Product whereProductDiscountPrice($value)
 * @method static Builder|Product whereSubcategoryId($value)
 * @property int $is_kaspi_visible
 * @property int $kaspi_product_price
 * @method static Builder|Product whereIsKaspiVisible($value)
 * @method static Builder|Product whereKaspiProductPrice($value)
 * @property int|null $supplier_id
 * @property string $meta_title
 * @property string|null $meta_description
 * @method static Builder|Product whereMetaDescription($value)
 * @method static Builder|Product whereMetaTitle($value)
 * @method static Builder|Product whereSupplierId($value)
 * @property string $product_name_web
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductNameWeb($value)
 */
	class Product extends \Eloquent {}
}

namespace App{
/**
 * App\ProductBatch
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int $store_id
 * @property int $purchase_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $arrival_id
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch ofProduct($product)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch ofStore($store)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch Positive()
 * @method static \Illuminate\Database\Query\Builder|ProductBatch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProductBatch withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductBatch withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch quantitiesOfStore($store_id)
 * @property-read \App\Store $store
 * @property-read \App\v2\Models\ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch positive()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch quantities()
 */
	class ProductBatch extends \Eloquent {}
}

namespace App{
/**
 * App\ProductGroup
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProductGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductGroup query()
 * @mixin \Eloquent
 */
	class ProductGroup extends \Eloquent {}
}

namespace App{
/**
 * App\ProductImage
 *
 * @property int $id
 * @property string $product_image
 * @property int $product_id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage whereProductImage($value)
 * @mixin \Eloquent
 */
	class ProductImage extends \Eloquent {}
}

namespace App{
/**
 * App\ProductQuantity
 *
 * @property int $id
 * @property int $product_id
 * @property int $store_id
 * @property int $quantity
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereStoreId($value)
 * @mixin \Eloquent
 */
	class ProductQuantity extends \Eloquent {}
}

namespace App{
/**
 * App\ProductTag
 *
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereTagId($value)
 * @mixin \Eloquent
 * @property-read mixed $taggable_type
 */
	class ProductTag extends \Eloquent {}
}

namespace App{
/**
 * App\ProductThumb
 *
 * @property int $id
 * @property string $product_image
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereProductImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ProductThumb extends \Eloquent {}
}

namespace App{
/**
 * App\Promocode
 *
 * @property int $id
 * @property string|null $promocode
 * @property int $client_id
 * @property int $discount
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $partner
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode ofPartner($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode wherePromocode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Promocode extends \Eloquent {}
}

namespace App{
/**
 * App\RatingCriteria
 *
 * @property int $id
 * @property string $criteria
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria query()
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria whereCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria whereId($value)
 * @mixin \Eloquent
 */
	class RatingCriteria extends \Eloquent {}
}

namespace App{
/**
 * App\Revision
 *
 * @property int $id
 * @property int $user_id
 * @property int $store_id
 * @property int $is_finished
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\RevisionProducts|null $revision_products
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision query()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereIsFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereUserId($value)
 * @mixin \Eloquent
 */
	class Revision extends \Eloquent {}
}

namespace App{
/**
 * App\RevisionProducts
 *
 * @property int $id
 * @property int $revision_id
 * @property int $product_id
 * @property int $stock_quantity
 * @property int $fact_quantity
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts whereFactQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts whereRevisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProducts whereStockQuantity($value)
 * @mixin \Eloquent
 */
	class RevisionProducts extends \Eloquent {}
}

namespace App{
/**
 * App\Sale
 *
 * @property int $id
 * @property int $client_id
 * @property int $store_id
 * @property int $user_id
 * @property int $discount
 * @property int $kaspi_red
 * @property string $kaspi_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $balance
 * @property int|null $partner_id
 * @property int $payment_type
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SaleProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Sale byDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereKaspiRed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $product_price
 * @property-read mixed $purchase_price
 * @property-read mixed $date
 * @method static \Illuminate\Database\Eloquent\Builder|Sale report()
 * @property-read mixed $discount_percent
 * @property-read mixed $final_price
 * @property-read mixed $margin
 * @method static \Illuminate\Database\Eloquent\Builder|Sale reportDate($dates)
 * @property array|null $split_payment
 * @property-read \App\v2\Models\Certificate $certificate
 * @property-read mixed $certificate_margin
 * @property-read \App\v2\Models\Certificate|null $used_certificate
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereSplitPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale reportSupplier($supplierProducts)
 * @property string $comment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $image
 * @property-read int|null $image_count
 * @property-read \App\v2\Models\Preorder $preorder
 * @method static \Illuminate\Database\Eloquent\Builder|Sale physicalSales()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereKaspiTransactionId($value)
 */
	class Sale extends \Eloquent {}
}

namespace App{
/**
 * App\SaleProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $sale_id
 * @property int $purchase_price
 * @property int $product_price
 * @property int $discount
 * @property-read Product $product
 * @property-read Sale $sale
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereSaleId($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|SaleProduct[] $brothers
 * @property-read int|null $brothers_count
 * @property-read mixed $final_price
 * @property-read mixed $margin
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereDiscount($value)
 * @property-read \App\ProductBatch $batch
 */
	class SaleProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Seller
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SellerRating[] $rating
 * @property-read int|null $rating_count
 * @method static \Illuminate\Database\Eloquent\Builder|Seller newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller query()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Seller extends \Eloquent {}
}

namespace App{
/**
 * App\SellerRating
 *
 * @property int $id
 * @property int $seller_id
 * @property int $criteria_id
 * @property int $rating
 * @property string $user_token
 * @property-read \App\RatingCriteria $criteria
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereCriteriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereUserToken($value)
 * @mixin \Eloquent
 */
	class SellerRating extends \Eloquent {}
}

namespace App{
/**
 * App\Sportsmen
 *
 * @property int $id
 * @property string $name
 * @property string $instagram
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Sportsmen extends \Eloquent {}
}

namespace App{
/**
 * App\Store
 *
 * @property int $id
 * @property string $city
 * @property int $type_id
 * @property string $name
 * @property string|null $address
 * @property string|null $description
 * @property string $kaspi_terminal_ip
 * @property int $ironBalance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $telegram_chat_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\StoreType $type
 * @method static \Illuminate\Database\Eloquent\Builder|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Store newQuery()
 * @method static \Illuminate\Database\Query\Builder|Store onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereTelegramChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Store withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Store withoutTrashed()
 * @mixin \Eloquent
 * @property int $city_id
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCityId($value)
 * @property-read \App\v2\Models\City $city_name
 * @property-read mixed $balance
 * @property-read mixed $iron_balance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CompanionTransaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Store shops()
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereKaspiTerminalIp($value)
 */
	class Store extends \Eloquent {}
}

namespace App{
/**
 * App\StoreType
 *
 * @property int $id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType query()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereType($value)
 * @mixin \Eloquent
 */
	class StoreType extends \Eloquent {}
}

namespace App{
/**
 * App\Subcategory
 *
 * @property int $id
 * @property string $subcategory_name
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $subcategory_slug
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory ofSlug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereSubcategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereSubcategorySlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_site_visible
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory site()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereIsSiteVisible($value)
 */
	class Subcategory extends \Eloquent {}
}

namespace App{
/**
 * App\SubcategoryProduct
 *
 * @property int $product_id
 * @property int $subcategory_id
 * @property-read Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct whereSubcategoryId($value)
 * @mixin \Eloquent
 * @property-read \App\Subcategory $subcategory
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct whereId($value)
 */
	class SubcategoryProduct extends \Eloquent {}
}

namespace App{
/**
 * App\Tag
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class Tag extends \Eloquent {}
}

namespace App{
/**
 * App\Transfer
 *
 * @property int $id
 * @property int $parent_store_id
 * @property int $child_store_id
 * @property int $user_id
 * @property int $is_confirmed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $photos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TransferBatch[] $batches
 * @property-read int|null $batches_count
 * @property-read \App\Store $child_store
 * @property-read \App\Store $parent_store
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereChildStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereIsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereParentStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereUserId($value)
 * @mixin \Eloquent
 * @property bool $is_accepted
 * @property-read \App\CompanionSale $companionSale
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereIsAccepted($value)
 */
	class Transfer extends \Eloquent {}
}

namespace App{
/**
 * App\TransferBatch
 *
 * @property int $id
 * @property int $transfer_id
 * @property int $batch_id
 * @property int $product_id
 * @property int $is_transferred
 * @property-read \App\Product $product
 * @property-read \App\ProductBatch $productBatch
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereIsTransferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereTransferId($value)
 * @mixin \Eloquent
 * @property int $discount
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereDiscount($value)
 */
	class TransferBatch extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property int $role_id
 * @property int $store_id
 * @property string|null $token
 * @property string $name
 * @property string $login
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\UserRole $role
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|User login($login)
 * @method static \Illuminate\Database\Eloquent\Builder|User sellers()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User token($token)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

namespace App{
/**
 * App\UserRole
 *
 * @property int $id
 * @property string $role_name
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereRoleName($value)
 * @mixin \Eloquent
 */
	class UserRole extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\AttributeValue
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $attribute_value
 * @property-read \App\Attribute $attribute_name
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereAttributeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereId($value)
 * @mixin \Eloquent
 */
	class AttributeValue extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\BaseProduct
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\AttributeValue[] $attributes
 * @property-read int|null $attributes_count
 * @property-read \App\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Sku[] $children
 * @property-read int|null $children_count
 * @property-read \App\Manufacturer $manufacturer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $price
 * @property-read int|null $price_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Thumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read \App\Subcategory $subcategory
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @method static Builder|BaseProduct inStock($store_id = 1)
 * @method static Builder|BaseProduct isHit($param = 'false')
 * @method static Builder|BaseProduct main()
 * @method static Builder|BaseProduct newModelQuery()
 * @method static Builder|BaseProduct newQuery()
 * @method static Builder|BaseProduct ofBrand($param)
 * @method static Builder|BaseProduct ofCategory($param)
 * @method static Builder|BaseProduct ofPrice($param)
 * @method static Builder|BaseProduct ofSearch($search)
 * @method static Builder|BaseProduct ofSubcategory($param)
 * @method static Builder|BaseProduct ofTag($search)
 * @method static \Illuminate\Database\Query\Builder|BaseProduct onlyTrashed()
 * @method static Builder|BaseProduct query()
 * @method static \Illuminate\Database\Query\Builder|BaseProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BaseProduct withoutTrashed()
 * @mixin \Eloquent
 */
	class BaseProduct extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Booking
 *
 * @property int $id
 * @property int $user_id
 * @property int $client_id
 * @property int $store_id
 * @property int $arrival_id
 * @property bool $is_sold
 * @property int $paid_sum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Arrival $arrival
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\BookingProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereIsSold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePaidSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUserId($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\BookingProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $booking_id
 * @property int $arrival_product_id
 * @property int $product_price
 * @property int $count
 * @property-read \App\v2\Models\Booking $booking
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereArrivalProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereProductPrice($value)
 */
	class BookingProduct extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\BrandMotivation
 *
 * @property int $id
 * @property array $amount
 * @property array $brands
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation whereBrands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation whereId($value)
 */
	class BrandMotivation extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Certificate
 *
 * @property int $id
 * @property string|null $barcode
 * @property int $user_id
 * @property int $amount
 * @property string $expired_at
 * @property bool $active
 * @property int $used_sale_id
 * @property int $sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $final_amount
 * @property-read mixed $used
 * @property-read \App\Sale $sale
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUsedSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUserId($value)
 * @mixin \Eloquent
 */
	class Certificate extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\City
 *
 * @property int $id
 * @property string $name
 * @property int $region_id
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereRegionId($value)
 * @mixin \Eloquent
 * @property int $delivery_cost
 * @property int $delivery_threshold
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeliveryCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeliveryThreshold($value)
 */
	class City extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Education
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $author_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\EducationAttachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\User $author
 * @method static \Illuminate\Database\Eloquent\Builder|Education newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Education newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Education query()
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Education extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\EducationAttachment
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $education_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereEducationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereUrl($value)
 * @mixin \Eloquent
 */
	class EducationAttachment extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Favorite
 *
 * @property int $id
 * @property int $product_id
 * @property string $user_token
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserToken($value)
 * @mixin \Eloquent
 */
	class Favorite extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Image
 *
 * @property int $id
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Image extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Loyalty
 *
 * @property int $id
 * @property string $name
 * @property int $discount
 * @property int $cashback
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereCashback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereUpdatedAt($value)
 */
	class Loyalty extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\News
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property string $short_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $news_image
 * @property-read int|null $news_image_count
 * @method static \Illuminate\Database\Eloquent\Builder|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News query()
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereShortText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NewsProduct[] $productNews
 * @property-read int|null $product_news_count
 */
	class News extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\OrderMessage
 *
 * @property int $id
 * @property string $chat_id
 * @property string $message
 * @property int $is_delivered
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereIsDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $order_id
 * @property-read \App\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereOrderId($value)
 */
	class OrderMessage extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Preorder
 *
 * @property int $id
 * @property int $client_id
 * @property int $user_id
 * @property int $store_id
 * @property int $payment_type
 * @property int $status 0 - в ожидании, 1 - подтверждено, -1 - отменено
 * @property string|null $comment
 * @property int $amount
 * @property int $sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\PreorderProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder query()
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereUserId($value)
 * @mixin \Eloquent
 */
	class Preorder extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\PreorderProduct
 *
 * @property int $id
 * @property int $preorder_id
 * @property int $product_id
 * @property-read \App\v2\Models\ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct wherePreorderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct whereProductId($value)
 * @mixin \Eloquent
 */
	class PreorderProduct extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\Product
 *
 * @property int $id
 * @property string $product_name
 * @property string $product_name_web
 * @property string|null $product_description
 * @property int $product_price
 * @property string $product_barcode
 * @property int|null $group_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_hit
 * @property int $is_site_visible
 * @property Carbon|null $deleted_at
 * @property-read Collection|AttributeProduct[] $attributes
 * @property-read int|null $attributes_count
 * @property-read Collection|Category $category
 * @property-read int|null $categories_count
 * @property-read Collection|Product[] $children
 * @property-read int|null $children_count
 * @property-read mixed $current_price
 * @property-read Collection|Manufacturer $manufacturer
 * @property-read int|null $manufacturer_count
 * @property-read Product|null $parent
 * @property-read Collection|Price[] $price
 * @property-read int|null $price_count
 * @property-read Collection|ProductImage[] $product_images
 * @property-read int|null $product_images_count
 * @property-read Collection|ProductQuantity[] $product_quantity
 * @property-read int|null $product_quantity_count
 * @property-read Collection|ProductThumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read Collection|ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read Collection|Subcategory $subcategory
 * @property-read int|null $subcategories_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tag_count
 * @method static Builder|Product inStock($store_id = 1)
 * @method static Builder|Product isHit($param = 'false')
 * @method static Builder|Product main()
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product ofBrand($param)
 * @method static Builder|Product ofCategory($param)
 * @method static Builder|Product ofPrice($param)
 * @method static Builder|Product ofSearch($search)
 * @method static Builder|Product ofSubcategory($param)
 * @method static Builder|Product ofTag($search)
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product with($relations)()
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereGroupId($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsHit($value)
 * @method static Builder|Product whereIsSiteVisible($value)
 * @method static Builder|Product whereProductBarcode($value)
 * @method static Builder|Product whereProductDescription($value)
 * @method static Builder|Product whereProductName($value)
 * @method static Builder|Product whereProductPrice($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 * @property-read int|null $tags_count
 * @property int|null $category_id
 * @property int|null $manufacturer_id
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereManufacturerId($value)
 * @property int $product_discount_price
 * @property int $grouping_attribute_id
 * @property int $subcategory_id
 * @property-read Collection|ProductSku[] $sku
 * @property-read int|null $sku_count
 * @method static Builder|Product whereGroupingAttributeId($value)
 * @method static Builder|Product whereProductDiscountPrice($value)
 * @method static Builder|Product whereSubcategoryId($value)
 * @property bool $is_kaspi_visible
 * @property int $kaspi_product_price
 * @property-read Collection|ProductBatch[] $batches
 * @property-read int|null $batches_count
 * @method static Builder|Product whereIsKaspiVisible($value)
 * @method static Builder|Product whereKaspiProductPrice($value)
 * @property int|null $supplier_id
 * @property string $meta_title
 * @property string|null $meta_description
 * @property-read \App\v2\Models\Supplier|null $supplier
 * @method static Builder|Product whereMetaDescription($value)
 * @method static Builder|Product whereMetaTitle($value)
 * @method static Builder|Product whereSupplierId($value)
 * @property-read \App\v2\Models\Favorite|null $favorite
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\ProductSaleEarning[] $seller_earning
 * @property-read int|null $seller_earning_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductNameWeb($value)
 */
	class Product extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\ProductAttribute
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_value_id
 * @property-read \App\v2\Models\AttributeValue $attribute_value
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereAttributeValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereProductId($value)
 * @mixin \Eloquent
 */
	class ProductAttribute extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\ProductSaleEarning
 *
 * @property int $id
 * @property int $product_id
 * @property int $percent
 * @property int $store_id
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning whereStoreId($value)
 */
	class ProductSaleEarning extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\ProductSku
 *
 * @property int $id
 * @property int $product_id
 * @property int $self_price
 * @property string $product_barcode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\AttributeValue[] $attributes
 * @property-read int|null $attributes_count
 * @property-read mixed $category
 * @property-read mixed $discount_price
 * @property-read mixed $grouping_attribute_id
 * @property-read mixed $is_hit
 * @property-read mixed $is_site_visible
 * @property-read mixed $manufacturer
 * @property-read mixed $product_description
 * @property-read mixed $product_name
 * @property-read mixed $product_price
 * @property-read mixed $subcategory
 * @property-read \App\v2\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProductSku onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereProductBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereSelfPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProductSku withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductSku withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $batches
 * @property-read int|null $batches_count
 * @property-read mixed $general_images
 * @property-read mixed $general_thumbs
 * @property-read mixed $prices
 * @property-read mixed $sku_count
 * @property-read mixed $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Thumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|ProductSku[] $relativeSku
 * @property-read int|null $relative_sku_count
 * @property-read mixed $all_attributes
 * @property-read mixed $is_kaspi_visible
 * @property-read mixed $kaspi_product_price
 * @property-read mixed $product_name_web
 */
	class ProductSku extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Region
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereName($value)
 * @mixin \Eloquent
 */
	class Region extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\RelatedProduct
 *
 * @property int $id
 * @property int $category_id
 * @property int $product_id
 * @property-read \App\v2\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct whereProductId($value)
 * @mixin \Eloquent
 */
	class RelatedProduct extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Shift
 *
 * @property int $id
 * @property int $user_id
 * @property int $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Store $store
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereUserId($value)
 * @mixin \Eloquent
 */
	class Shift extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\ShiftPenalty
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount Отрицательные значения для штрафов, положительные для премий
 * @property int $author_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $author
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $date
 */
	class ShiftPenalty extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\ShiftTax
 *
 * @property int $id
 * @property int $shift_tax Стоимость смены
 * @property int $sale_percent Процент от продаж
 * @property int $store_id
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereSalePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereShiftTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereStoreId($value)
 * @mixin \Eloquent
 * @property-read mixed $calculated_sale_percent
 */
	class ShiftTax extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Sku
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\AttributeValue[] $attributes
 * @property-read int|null $attributes_count
 * @property-read mixed $category
 * @property-read mixed $discount_price
 * @property-read mixed $grouping_attribute_id
 * @property-read mixed $is_hit
 * @property-read mixed $is_site_visible
 * @property-read mixed $manufacturer
 * @property-read mixed $product_description
 * @property-read mixed $product_name
 * @property-read mixed $product_price
 * @property-read mixed $subcategory
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $price
 * @property-read int|null $price_count
 * @property-read \App\v2\Models\BaseProduct $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Thumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sku query()
 * @mixin \Eloquent
 */
	class Sku extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Supplier
 *
 * @property int $id
 * @property string $supplier_name
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereUserId($value)
 * @mixin \Eloquent
 */
	class Supplier extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Task
 *
 * @property int $id
 * @property int $author_id
 * @property int $user_id
 * @property int $store_id
 * @property string $date_start
 * @property string $date_finish
 * @property bool $is_completion_required
 * @property bool $is_completed
 * @property string $text
 * @property string $title
 * @property string|null $assets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\TaskAttachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\User $author
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAssets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDateFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsCompletionRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUserId($value)
 * @mixin \Eloquent
 */
	class Task extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\TaskAttachment
 *
 * @property int $id
 * @property string $url
 * @property string $name
 * @property int $task_id
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereUrl($value)
 * @mixin \Eloquent
 */
	class TaskAttachment extends \Eloquent {}
}

namespace App\v2\Models{
/**
 * App\v2\Models\Thumb
 *
 * @property int $id
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb query()
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $image_id
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereImageId($value)
 */
	class Thumb extends \Eloquent {}
}

