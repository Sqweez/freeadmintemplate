<?php

namespace App\Jobs\Revision;

use App\Category;
use App\v2\Models\ProductSku;
use App\v2\Models\Revision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateRevisionExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Revision $revision;
    protected int $categoryId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Revision $revision, int $categoryId)
    {
        $this->revision = $revision;
        $this->categoryId = $categoryId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $this->createRevisionFile();
        $this->updateRevisionStatus();

    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function createRevisionFile()
    {
        $products = $this->getProducts();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '#000000'],
                ],
            ],
        ];
        $sheet->setCellValue('A1', 'Артикул');
        $sheet->setCellValue('B1', '№');
        $sheet->setCellValue('C1', 'Наименование');
        $sheet->setCellValue('D1', 'Стоимость');
        $sheet->setCellValue('E1', 'Количество');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setVisible(false);
        $sheet->fromArray($products, null, 'A2');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:E' . $highestRow)->applyFromArray($styleArray);
        $writer = new Xlsx($spreadsheet);
        $filename = $this->getFileName();
        $path = "revisions/generated/{$this->revision->id}";
        if (!\Storage::disk('public')->exists($path)) {
            \Storage::disk('public')->makeDirectory($path);
        }
        $fullPath = storage_path("app/public/{$path}/{$filename}");
        $writer->save($fullPath);
        $this->revision->files()->create([
            'category_id' => $this->categoryId,
            'original_file_path' => "${path}/{$filename}"
        ]);
    }

    private function getFileName(): string
    {
        $category = Category::find($this->categoryId);
        return sprintf("Файл_ревизии_№%s_%s.xlsx", $this->revision->id, $category->name);
    }

    private function getProducts(): array
    {
        return ProductSku::query()
            ->select(['product_sku.*', 'manufacturers.id as manufacturer_id'])
            ->join('products', 'product_sku.product_id', '=', 'products.id')
            ->join('manufacturers', 'products.manufacturer_id', '=', 'manufacturers.id')
            ->whereHas('product', function ($q) {
                return $q->where('category_id', $this->categoryId)->where('is_opt', false);
            })
            ->whereHas('batches', function ($q) {
                return $q->where('store_id', $this->revision->store_id)->where('quantity', '>', 0);
            })
            ->with([
                'attributes',
                'product.attributes',
                'product.manufacturer',
            ])
            ->orderBy('manufacturers.id', 'asc')
            ->get()
            ->map(function (ProductSku $productSku, $key) {
                return [
                    'sku' => $productSku->id,
                    'key' => $key + 1,
                    'name' => sprintf(
                        "%s %s %s %s",
                        $productSku->product->manufacturer->manufacturer_name,
                        $productSku->product->product_name,
                        $productSku->product->attributes->pluck('attribute_value')->join(' '),
                        $productSku->attributes->pluck('attribute_value')->join(' '),
                    ),
                    'price' => number_format($productSku->product->product_price, 0, '', ' '),
                    'count' => 0,
                ];
            })
            ->toArray();
    }

    private function updateRevisionStatus(): void
    {
        $remainingJobs = Category::where('id', '>', $this->categoryId)->count();
        if ($remainingJobs === 0) {
            $this->revision->update([
                'status' => 'created'
            ]);
        }
    }
}
