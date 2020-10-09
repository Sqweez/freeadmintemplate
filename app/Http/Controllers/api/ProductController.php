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
use App\ProductThumb;
use App\SubcategoryProduct;
use App\SaleProduct;
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
                ->with(['attributes', 'manufacturer', 'categories', 'subcategories', 'quantity', 'price'])
                ->get()
        );
    }

    public function store(Request $request) {
        $product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'product_images', 'product_thumbs', 'prices']);
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
        $product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'id', 'groupProduct', 'product_images', 'product_thumbs', 'prices']);
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
        $_product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'product_images', 'product_thumbs', 'prices']);
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
        $_product = $request->except(['id', 'categories', 'subcategories', 'manufacturer', 'attributes', 'product_images', 'product_thumbs', 'product_barcode', 'prices']);
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
