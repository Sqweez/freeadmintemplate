<?php

namespace App\Http\Controllers\Services;

use App\AttributeProduct;
use App\Category;
use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Manufacturer;
use App\ManufacturerProducts;
use App\Product;
use App\ProductBatch;
use App\Subcategory;
use App\SubcategoryProduct;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function transferProducts() {
        Product::truncate();
        ProductBatch::truncate();
        CategoryProduct::truncate();
        SubcategoryProduct::truncate();
        ManufacturerProducts::truncate();
        $file = json_decode(utf8_encode(file_get_contents('http://iron.ariesdev.kz/admin/transfer.php')), true);
        $products = $file['products'];
        $categories = $file['categories'];
        $subcategories = $file['subcategories'];
        foreach ($products as $product) {
            $_product = Product::create([
                'product_name' => $product['product_name'],
                'product_description' => base64_decode($product['product_opisanie']),
                'product_price' => $product['prodazhnaya_cena'],
                'product_barcode' => $product['product_shtrih']
            ]);
            $product_id = $_product['id'];
            $_product->update(['group_id' => $product_id]);
            $key = array_search($product['product_category_id'], array_column($categories, 'category_id'));
            $category_name = $categories[$key]['category_name'];
            $category_id = Category::where('category_name', $category_name)->first()['id'];
            CategoryProduct::create(['product_id' => $product_id, 'category_id' => $category_id]);
            /**/
            $key = array_search($product['pod_category_id'], array_column($subcategories, 'pod_category_id'));
            $subcategory_name = $subcategories[$key]['pod_category_name'];
            $subcategory_id = Subcategory::where('subcategory_name', $subcategory_name)->first()['id'];
            SubcategoryProduct::create(['product_id' => $product_id, 'subcategory_id' => $subcategory_id]);
            AttributeProduct::create(['product_id' => $product_id, 'attribute_id' => 1, 'attribute_value' => $product['product_vkus']]);
            AttributeProduct::create(['product_id' => $product_id, 'attribute_id' => 2, 'attribute_value' => $product['ves_kolvo_tabletok']]);
            $manufacturer_id = Manufacturer::where('manufacturer_name', $product['manufacturer'])->first()['id'];
            if ($manufacturer_id !== null) {
                ManufacturerProducts::create(['manufacturer_id' => $manufacturer_id, 'product_id' => $product_id]);
            } else {
                $manufacturer_id = Manufacturer::create(['manufacturer_name' => $product['manufacturer']])['id'];
                ManufacturerProducts::create(['manufacturer_id' => $manufacturer_id, 'product_id' => $product_id]);
            }
            if ($product['count'] > 0) {
                ProductBatch::create(['product_id' => $product_id, 'quantity' => $product['count'], 'store_id' => 1, 'purchase_price' => $product['purchase_price']]);

            }
        }
    }


    public function transferClients() {
        $clients = json_decode(utf8_encode(file_get_contents('http://iron.ariesdev.kz/admin/transfer.php?action=clients')), true);
        foreach ($clients as $client) {
            if (strlen($client['name']) > 0) {
                $_client = Client::create([
                    'client_name' => $client['name'],
                    'client_phone' => $client['telefon'],
                    'client_card' => $client['card_id'],
                    'client_discount' => $client['discountPercent'] ?? 0
                ]);
                $sum = intval($client['total_sum_client']);
                if ($sum > 0) {
                    ClientTransaction::create([
                        'amount' => $sum,
                        'user_id' => 1,
                        'client_id' =>$_client->id,
                        'sale_id' => -1,
                    ]);
                }
            }
        }
        }
}
