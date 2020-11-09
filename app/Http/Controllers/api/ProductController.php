<?php

namespace App\Http\Controllers\api;

use App\AttributeProduct;
use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ExcelService;
use App\Http\Resources\MainProductsResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductRevisionResource;
use App\ManufacturerProducts;
use App\Price;
use App\Product;
use App\ProductBatch;
use App\ProductImage;
use App\ProductTag;
use App\ProductThumb;
use App\SubcategoryProduct;
use App\SaleProduct;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller {

    public function index() {
        return ProductResource::collection(
            Product::orderBy('group_id')
                ->with(['attributes', 'manufacturer', 'categories', 'subcategories', 'quantity', 'price', 'tag'])
                ->get()
        );
    }

    public function bombbarReport() {
        $product_ids = [
            1778, 821, 780, 774, 844, 805, 640, 1773, 1459
        ];

        $products = Product::whereIn('group_id', $product_ids)->pluck('id');
        $batches = collect(ProductBatch::whereIn('product_id', $products)
            ->where('quantity', '>', 0)
            ->whereIn('store_id', [1, 2, 3, 4, 5, 8, 9])
            ->with(['product' => function ($q) {
                $q->select(['id', 'product_name', 'group_id']);
            }])
            ->get());
        $batches = $batches->groupBy('product.group_id')->map(function ($batch) {
            return collect($batch)->groupBy('store_id');
        })->map(function ($batch) {
            //$product_name = $batch[0]['product'];
            $quantity = collect($batch)->map(function ($_batch) {
                return collect($_batch)->reduce(function ($i, $a) {
                    return $a['quantity'] + $i;
                }, 0);
            });
            return [
                'quantity' => $quantity,
                'product_name' => (collect($batch)->first())->first()['product']['product_name'],
            ];
        });

        return $batches;
        /*$sales = SaleProduct::whereIn('product_id', $products)->with(['products' => function ($query) {
            $query->select(['id', 'product_name', 'group_id']);
        }])->whereHas('sale', function ($query) {
            return $query->where('discount', '!=', 100);
        })->get();
        $sales = collect($sales)->groupBy('product_id');
        $sales = $sales->map(function ($sale) {
            $count = count($sale);
            $total_purchase_cost = collect($sale)->reduce(function ($i, $a) {
                return $a['purchase_price'] + $i;
            }, 0);
            $total_cost = collect($sale)->reduce(function ($i, $a) {
                return $a['product_price'] + $i;
            }, 0);
            $group_id = $sale[0]['products']['group_id'];
            $product_name = $sale[0]['products']['product_name'];
            return [
                'count' => $count,
                'total_purchase_cost' => $total_purchase_cost,
                'total_cost' => $total_cost,
                'group_id' => $group_id,
                'product_name' => $product_name
            ];
        })->groupBy('group_id')
        ->map(function ($product) {
            $count = $product->reduce(function ($i, $a) {
                return $i + $a['count'];
            }, 0);
            $purchase_price = $product->reduce(function ($i, $a) {
                return $i + $a['total_purchase_cost'];
            }, 0);
            $product_price = $product->reduce(function ($i, $a) {
                return $i + $a['total_cost'];
            }, 0);
            $product_name = $product[0]['product_name'];
            return [
                'product_name' => $product_name,
                'purchase_price' => $purchase_price,
                'product_price' => $product_price,
                'count' => $count
            ];
        });
        return $sales; */
    }

    public function setTags() {
        $products = collect(Product::all());
        $brn = $products->map(function ($product) {
            $product_name = $product['product_name'];
            $manufacturer = $product->manufacturer[0] ?? null;
            $category = $product->categories[0] ?? null;
            $subcategory = $product->subcategories[0] ?? null;
            $tag = Tag::where('name', 'like', '%' . $product_name . '%')->first();
            if ($tag) {
                ProductTag::create(
                    [
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]
                );
            } else {
                $tag = Tag::create(['name' => $product_name]);
                ProductTag::create(
                    [
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]
                );
            }

            if ($manufacturer !== null) {
                $tag = Tag::where('name', 'like', '%' . $manufacturer['manufacturer_name'])->first();

                if ($tag) {
                    ProductTag::create([
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]);
                } else {
                    $tag = Tag::create(['name' => $manufacturer['manufacturer_name']]);
                    ProductTag::create([
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]);
                }
            }

            if ($category !== null) {
                $tag = Tag::where('name', 'like', '%' . $category['category_name'])->first();

                if ($tag) {
                    ProductTag::create([
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]);
                } else {
                    $tag = Tag::create(['name' => $category['category_name']]);
                    ProductTag::create([
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]);
                }
            }

            if ($subcategory !== null) {
                $tag = Tag::where('name', 'like', '%' . $subcategory['subcategory_name'])->first();

                if ($tag) {
                    ProductTag::create([
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]);
                } else {
                    $tag = Tag::create(['name' => $subcategory['subcategory_name']]);
                    ProductTag::create([
                        'tag_id' => $tag['id'],
                        'product_id' => $product['id']
                    ]);
                }
            }


        });
        return $brn;

    }

    public function store(Request $request) {
        $product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'product_images', 'product_thumbs', 'prices', 'tags']);
        $_product = Product::create($product);
        $product_id = $_product['id'];
        $_product->update(['group_id' => $product_id]);
        $this->createAdditionalFields($request, $product_id);
        return new ProductResource($_product);
    }

    private function storeImages($images, $product_id) {
        if (count($images) === 0) {
            ProductImage::create(['product_image' => 'products/product_image_default.jpg', 'product_id' => $product_id]);
        }
        foreach ($images as $image) {
            ProductImage::create(['product_image' => $image, 'product_id' => $product_id]);
        }
    }

    private function storeThumbs($images, $product_id) {
        if (count($images) === 0) {
            ProductThumb::create(['product_image' => 'product_thumbs/product_image_default.webp', 'product_id' => $product_id]);
            return;
        }

        $thumbs = $this->makeThumbs($images);
        foreach ($thumbs as $image) {
            ProductThumb::create(['product_image' => $image, 'product_id' => $product_id]);
        }
    }

    private function createCategoryProducts($categories, $id) {
        foreach ($categories as $category) {
            CategoryProduct::create(['category_id' => $category, 'product_id' => $id]);
        }
    }

    private function createSubcategoryProduct($subcategories, $id) {
        foreach ($subcategories as $subcategory) {
            SubcategoryProduct::create(['subcategory_id' => $subcategory, 'product_id' => $id]);
        }
    }

    private function createAttributeProduct($attributes, $id) {
        foreach ($attributes as $attribute) {
            if (strlen($attribute['attribute_value']) > 0) {
                AttributeProduct::create(['product_id' => $id, 'attribute_id' => $attribute['attribute_id'], 'attribute_value' => $attribute['attribute_value']]);
            }
        }
    }

    private function createManufacturerProduct($manufacturer, $id) {
        if ($manufacturer)
            ManufacturerProducts::create(['product_id' => $id, 'manufacturer_id' => $manufacturer]);
    }

    public function createBatch(Request $request) {
        ProductBatch::create($request->all());
    }

    public function createRange(Request $request) {
        $product_id = $request->get('id');
        $product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'id', 'groupProduct', 'product_images', 'product_thumbs', 'prices', 'tags']);
        $groupProduct = $request->get('groupProduct');
        if ($groupProduct === true) {
            $product['group_id'] = $product_id;
        }
        $_product = Product::create($product);
        if ($groupProduct === false) {
            $_product->update(['group_id' => $_product['id']]);
        }
        $product_id = $_product['id'];
        $this->createAdditionalFields($request, $product_id);
        return new ProductResource($_product);
    }

    private function createAdditionalFields(Request $request, $product_id) {
        $product_images = $request->get('product_images');
        $this->storeImages($product_images, $product_id);
        $this->storeThumbs($product_images, $product_id);
        $categories = $request->get('categories');
        $this->createCategoryProducts($categories, $product_id);
        $subcategories = $request->get('subcategories');
        $this->createSubcategoryProduct($subcategories, $product_id);
        $attributes = $request->get('attributes');
        $this->createAttributeProduct($attributes, $product_id);
        $manufacturer = $request->get('manufacturer');
        $this->createManufacturerProduct($manufacturer, $product_id);
        $prices = $request->get('prices');
        $this->createPricesProducts($prices, $product_id);
        $tags = $request->get('tags');
        $this->createProductTag($tags, $product_id);
    }

    private function createProductTag($tags, $product_id) {
        collect($tags)->each(function ($tag) use ($product_id) {
            if (!isset($tag['id'])) {
                $_tag = Tag::where('name', $tag['name'])->first();
                if ($_tag) {
                    ProductTag::create([
                        'product_id' => $product_id,
                        'tag_id' => $_tag['id']
                    ]);
                } else {
                    $_tag = Tag::create([
                        'name' => $tag['name']
                    ]);
                    ProductTag::create([
                        'product_id' => $product_id,
                        'tag_id' => $_tag['id']
                    ]);
                }
            } else {
                ProductTag::create([
                    'product_id' => $product_id,
                    'tag_id' => $tag['id']
                ]);
            }
        });
    }

    private function createPricesProducts($prices, $product_id) {
        $_prices = array_filter($prices, function ($i) {
            return $i['store_id'] & $i['price'];
        });
        foreach ($_prices as $price) {
            Price::create([
                'product_id' => $product_id,
                'store_id' => $price['store_id'],
                'price' => $price['price']
            ]);
        }
    }

    public function update(Request $request, Product $product) {
        $_product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'product_images', 'product_thumbs', 'prices', 'tags']);
        $product_id = $request->get('id');
        $product->update($_product);
        $this->deleteDuplicates($product_id);
        $this->createAdditionalFields($request, $product_id);
        $this->updateGroups($request);
        return ProductResource::collection(Product::where('group_id', $product->group_id)->get());
    }

    private function updateGroups(Request $request) {
        $product_id = $request->get('id');
        $group_id = Product::find($product_id)['group_id'];
        $_product = $request->except(['id', 'categories', 'subcategories', 'manufacturer', 'attributes', 'product_images', 'product_thumbs', 'product_barcode', 'prices', 'tags']);
        Product::where('group_id', $group_id)->where('id', '!=', $product_id)->update($_product);
        $products = Product::where('group_id', $group_id)->where('id', '!=', $product_id)->get();
        collect($products)->map(function ($product) use ($request){
            ProductThumb::where('product_id', $product['id'])->delete();
            ProductImage::where('product_id', $product['id'])->delete();
            $this->storeImages($request->get('product_images'), $product['id']);
            $_product = Product::find($product['group_id']);
            $thumbs = $_product->product_thumbs->pluck('product_image');
            foreach ($thumbs as $thumb) {
                ProductThumb::create(['product_id' => $product['id'], 'product_image' => $thumb]);
            }
        });
    }

    public function destroy(Product $product) {
        AttributeProduct::where('product_id', $product['id'])->delete();
        CategoryProduct::where('product_id', $product['id'])->delete();
        ManufacturerProducts::where('product_id', $product['id'])->delete();
        SubcategoryProduct::where('product_id', $product['id'])->delete();
        ProductBatch::where('product_id', $product['id'])->delete();
        SaleProduct::where('product_id', $product['id'])->delete();
        ProductThumb::where('product_id', $product['id'])->delete();
        $product->delete();
    }

    private function deleteDuplicates($id) {
        ProductImage::where('product_id', $id)->delete();
        AttributeProduct::where('product_id', $id)->delete();
        CategoryProduct::where('product_id', $id)->delete();
        ManufacturerProducts::where('product_id', $id)->delete();
        SubcategoryProduct::where('product_id', $id)->delete();
        ProductThumb::where('product_id', $id)->delete();
        Price::where('product_id', $id)->delete();
        ProductTag::where('product_id', $id)->delete();
    }

    private function makeThumbs($images) {
        $output = [];
        foreach ($images as $image) {
            $output[] = $this->generateThumb($image);
        }

        return $output;
    }

    private function generateThumb($image) {
        try {
            $img = Storage::get('public/' . $image);

        } catch (\Exception $exception) {
            return null;
        }

        $correctImage = Image::make($img);

        $resizedImage = $correctImage->fit(170, 170);
        $imagePath = public_path('storage/product_thumbs/');
        $imageName = Str::random(40) . '.webp';
        $resizedImage->save($imagePath . $imageName);
        return 'product_thumbs/' . $imageName;
    }

    public function setThumbsAll() {
        $productImages = ProductImage::where('product_image', '!=', 'products/product_image_default.jpg')->get();
        $productImages->map(function ($i) {
            $thumbPath = $this->generateThumb($i['product_image']);
            if ($thumbPath !== null) {
                ProductThumb::create(['product_id' => $i['product_id'], 'product_image' => $thumbPath]);
            }
        });
    }

    public function jsonProducts() {
        $products = collect(ProductRevisionResource::collection(Product::all()));
        $jsonData = json_encode($products, JSON_UNESCAPED_UNICODE);
        $fileName = Carbon::now()->toDateString() . '_' . Str::random(10) . '_'  .'_products.json';
        $fileName = 'public/json/' . $fileName;
        Storage::put($fileName, $jsonData);
        $excelService = new ExcelService();
        $excelService->createExcel($fileName);
        return $products;
    }

    public function excelProducts(Request $request) {
        $excelService = new ExcelService();
        return $excelService->parseExcel($request->get('filename'));
    }

    public function jsonParseProduct(Request $request) {
        $fileName = $request->get('file');
        $store_id = $request->get('store_id') ?? 2;
        $jsonContent = Storage::get('public/json/' . $fileName . '.json');
        $batches = json_decode($jsonContent, true);
        foreach ($batches as $batch) {
            $purchase_price = ProductBatch::where('product_id', $batch['id'])->first()['purchase_price'] ?? 0;
            ProductBatch::create([
                'product_id' => $batch['id'],
                'quantity' => $batch['count'],
                'purchase_price' => $purchase_price,
                'store_id' => $store_id
            ]);
        }
        return $jsonContent;
    }

    public function getMainProducts(Request $request) {
        return MainProductsResource::collection(Product::Main()->with(['manufacturer', 'categories', 'subcategories'])->get());
    }
}
