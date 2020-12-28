<?php
namespace database\seeds;

use App\Sale;
use App\SaleProduct;
use Illuminate\Database\Seeder;

class SaleDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sales =  Sale::where('discount', '!=', 0)->cursor();
        foreach ($sales as $sale) {
            SaleProduct::where('sale_id', $sale['id'])->update([
                'discount' => $sale['discount']
            ]);
        }
    }
}
