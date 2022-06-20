<?php


namespace App\Http\Controllers\Services;

use App\Http\Resources\v2\Product\ProductsResource;
use App\Price;
use App\ProductBatch;
use App\v2\Models\Product;
use App\Tag;
use App\v2\Models\AttributeValue;
use App\v2\Models\Image;
use App\v2\Models\ProductSku;
use App\v2\Models\Thumb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService {

    public function all() {
        return (ProductSku::with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)
            ->orderBy('product_id')
            ->orderBy('id')
            ->get()
            ->sortBy('product_name')
        );
    }

    public function get($id) {
        return ProductSku::withCount('relativeSku')->whereId($id)->first();
    }

    public function create(array $_product, array $_attributes) {
        try {
            DB::beginTransaction();
            $product = Product::create($_product);
            $this->updateProductRelations($product, $_attributes);
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json([
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
                'stacktrace' => $e->getTraceAsString()
            ], 412);
        }
    }

    public function attachTags($products, $tags) {
        $_tags = collect($tags)->map(function ($i) {
            unset($i['id']);
            return Tag::whereName($i['name'])->firstOrCreate($i)->id;
        })->values()->all();
        collect($products)->each(function (Product $product) use ($_tags) {
            $product->tags()->syncWithoutDetaching($_tags);
        });
    }

    private function createTags(Product $product) {
        $tags = [];
        array_push($tags, ['name' => $product->product_name]);
        array_push($tags, ['name' => $product->manufacturer->manufacturer_name]);
        array_push($tags, ['name' => $product->category->category_name]);
        array_push($tags, ['name' => $product->subcategory->subcategory_name]);
        if (strlen($product->product_name_web)) {
            array_push($tags, ['name' => $product->product_name_web]);
        }
        return $tags;
    }

    public function updateProduct(Product $product, array $_attributes, array $fields) {
        try {
            DB::beginTransaction();
            $product->update($_attributes);
            $this->updateProductRelations($product, $fields);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
                'stacktrace' => $e->getTraceAsString()
            ], 412);
        }

    }

    public function createSku(Product $product, array $_attributes) {
        try {
            DB::beginTransaction();
            $product_sku = $this->createProductSku($product, $_attributes);
            $this->updateProductSkuRelations($product_sku, $_attributes, $product->grouping_attribute_id);
            DB::commit();
            return new ProductsResource($product_sku);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
                'stacktrace' => $e->getTraceAsString()
            ], 412);
        }
    }

    public function updateSku(ProductSku $productSku, array $_attributes) {
        try {
            DB::beginTransaction();
            $productSku->update([
                'product_barcode' => $_attributes['product_barcode']
            ]);
            $this->updateProductSkuRelations($productSku, $_attributes, $productSku->grouping_attribute_id);
            DB::commit();
            $productSku->fresh();
            return new ProductsResource($productSku);
        } catch (\Exception $exception) {
            DB::rollBack();
        }
    }

    private function updateProductRelations(Product $product, array $fields) {
        $tags = $this->createTags($product);
        $this->syncTags($product, array_merge($fields[Product::TAG], $tags));
        $this->syncProductImages($product, $fields[Product::PRODUCT_IMAGES]);
        $this->syncProductThumbs($product, $fields[Product::PRODUCT_THUMBS]);
        $this->syncProductPrices($product, $fields[Product::PRICE]);
        $this->syncAttributes($product, $this->getProductAttributes($product->grouping_attribute_id, $fields[Product::ATTRIBUTES]));
        $this->syncAdditionalSubcategories($product, $fields['additional_subcategories']);
        $product->push();
    }

    private function syncAdditionalSubcategories(Product $product, array $subcategories) {
        $product->additionalSubcategories()->sync($subcategories);
    }

    private function updateProductSkuRelations(ProductSku $productSku, array $fields, $attribute_id) {
        $this->syncProductImages($productSku, $fields[ProductSku::PRODUCT_SKU_IMAGES]);
        $this->syncProductThumbs($productSku, $fields[ProductSku::PRODUCT_SKU_THUMBS]);
        $this->syncAttributes($productSku, $this->getProductSkuAttributes($attribute_id, $fields[Product::ATTRIBUTES]));
        $productSku->push();
    }

    public function createProductSku(Product $product, array $fields): ProductSku {
        $fields['product_id'] = $product->id;
        return ProductSku::create($fields);
    }

    private function getProductAttributes($id, array $attributes): array {
        return collect($attributes)
            ->filter(function ($attr) use ($id) {
                return $attr['attribute_id'] !== $id;
            })
            ->values()
            ->all();
    }

    private function getProductSkuAttributes($id, array $attributes) {
        return collect($attributes)
            ->filter(function ($attr) use ($id) {
                return $attr['attribute_id'] === $id;
            })
            ->values()
            ->all();
    }

    private function syncTags(Product $product, $tags) {
        $tags = collect($tags)->map(function ($i) {
            unset($i['id']);
            return Tag::whereName($i['name'])->firstOrCreate($i)->id;
        })->values()->all();

        $product->tags()->sync($tags);
    }

    private function syncAttributes($product, array $attributes) {
        $attributes = collect($attributes)->map(function ($i) {
            $attributeValueQuery = AttributeValue::query();
            $attributeValueQuery->where('attribute_value', 'like' ,  "%" . $i['attribute_value'] . "%")
                ->where('attribute_id', $i['attribute_id']);
            return $attributeValueQuery->firstOrCreate($i)->id;
        });


        $product->attributes()->sync($attributes);
    }

    private function syncProductImages($product, array $images) {
        $images = collect($images)->map(function ($i) {
            return Image::whereImage($i['image'])->firstOrCreate($i)->id;
        });

        $product->product_images()->sync($images);
    }

    private function syncProductThumbs($product, array $images) {
        $images = collect($images)->map(function ($i) {
            return Thumb::whereImage($i['image'])->firstOrCreate($i)->id;
        });

        $product->product_thumbs()->sync($images);
    }

    private function syncProductPrices(Product $product, array $prices) {
        Price::where('product_id', $product->id)->delete();
        $product_id = $product->id;
        collect($prices)->each(function ($price) use ($product_id) {
            Price::create([
                'product_id' => $product_id,
                'price' => $price['price'],
                'store_id' => $price['store_id']
            ]);
        });
    }

    public function getProductFields(Request $request): array{
        $product = $request->only([
            Product::PRODUCT_NAME,
            Product::PRODUCT_DESCRIPTION,
            Product::PRODUCT_PRICE,
            Product::IS_HIT,
            Product::IS_SITE_VISIBLE,
            Product::GROUPING_ATTRIBUTE_ID,
            Product::KASPI_PRODUCT_PRICE,
            Product::IS_KASPI_VISIBLE,
            Product::SUPPLIER_ID,
            Product::META_TITLE,
            Product::META_DESCRIPTION,
            Product::PRODUCT_NAME_WEB,
            Product::IS_IHERB
        ]);

        $product[Product::CATEGORY_ID] = $request->get(Product::CATEGORY);
        $product[Product::SUBCATEGORY_ID] = $request->get(Product::SUBCATEGORY);
        $product[Product::MANUFACTURER_ID] = $request->get(Product::MANUFACTURER);
        return $product;
    }

    public function getRelationFields(Request $request): array {
        return $request->only([
            Product::TAG,
            Product::ATTRIBUTES,
            Product::PRODUCT_IMAGES,
            Product::PRODUCT_THUMBS,
            Product::PRICE,
            Product::PRODUCT_BARCODE,
            'additional_subcategories'

         /*   ProductSku::PRODUCT_SKU_IMAGES,
            ProductSku::PRODUCT_SKU_THUMBS*/
        ]);
    }

    public function getSkuFields(Request $request): array {
        return $request->only([
            Product::ATTRIBUTES,
            Product::PRODUCT_BARCODE,
            ProductSku::PRODUCT_SKU_IMAGES,
            ProductSku::PRODUCT_SKU_THUMBS
        ]);
    }

    private function getSkuAttributes($_fields): array {
        return collect($_fields)
            ->only([Product::PRODUCT_BARCODE])
            ->values()
            ->all();
    }

    public function delete($id) {
        ProductSku::find($id)->delete();
    }


    public function addQuantity($product_id, $store_id, $quantity, $purchase_price) {
        return ProductBatch::create([
            'product_id' => $product_id,
            'store_id' => $store_id,
            'quantity' => $quantity,
            'purchase_price' => $purchase_price
        ]);
    }


    public function getQuantityByProduct($product_id, $store_id) {
        return ProductSku::find($product_id)->getQuantity($store_id);
    }
}
