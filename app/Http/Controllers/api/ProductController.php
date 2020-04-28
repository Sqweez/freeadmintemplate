<?php

namespace App\Http\Controllers\api;

use App\AttributeProduct;
use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\ManufacturerProducts;
use App\Product;
use App\ProductBatch;
use App\ProductImage;
use App\SubcategoryProduct;
use Illuminate\Http\Request;

class ProductController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index() {
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ProductResource
     */
    public function store(Request $request) {
        $product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'product_images']);
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
            AttributeProduct::create(['product_id' => $id, 'attribute_id' => $attribute['attribute_id'], 'attribute_value' => $attribute['attribute_value']]);
        }
    }

    private function createManufacturerProduct($manufacturer, $id) {
        ManufacturerProducts::create(['product_id' => $id, 'manufacturer_id' => $manufacturer]);
    }

    public function createBatch(Request $request) {
        ProductBatch::create($request->all());
    }

    public function createRange(Request $request) {
        $product_id = $request->get('id');
        $product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'id', 'groupProduct', 'product_images']);
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
        $categories = $request->get('categories');
        $this->createCategoryProducts($categories, $product_id);
        $subcategories = $request->get('subcategories');
        $this->createSubcategoryProduct($subcategories, $product_id);
        $attributes = $request->get('attributes');
        $this->createAttributeProduct($attributes, $product_id);
        $manufacturer = $request->get('manufacturer');
        $this->createManufacturerProduct($manufacturer, $product_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @return void
     */
    public function update(Request $request, Product $product) {
        $_product = $request->except(['categories', 'subcategories', 'manufacturer', 'attributes', 'product_images']);
        $product_id = $request->get('id');
        $product->update($_product);
        $this->deleteDuplicates($product_id);
        $this->createAdditionalFields($request, $product_id);
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return void
     * @throws \Exception
     */
    public function destroy(Product $product) {
        AttributeProduct::where('product_id', $product['id'])->delete();
        CategoryProduct::where('product_id', $product['id'])->delete();
        ManufacturerProducts::where('product_id', $product['id'])->delete();
        SubcategoryProduct::where('product_id', $product['id'])->delete();
        ProductBatch::where('product_id', $product['id'])->delete();
        $product->delete();
    }

    private function deleteDuplicates($id) {
        ProductImage::where('product_id', $id)->delete();
        AttributeProduct::where('product_id', $id)->delete();
        CategoryProduct::where('product_id', $id)->delete();
        ManufacturerProducts::where('product_id', $id)->delete();
        SubcategoryProduct::where('product_id', $id)->delete();
    }
}
