<?php

namespace App\Jobs\Revision;

use App\v2\Models\ProductSku;
use App\v2\Models\RevisionFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProcessRevisionFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected RevisionFile $revisionFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RevisionFile $revisionFile)
    {
        $this->revisionFile = $revisionFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $this->processFile();
        $this->revisionFile->processed_at = now();
        $this->revisionFile->save();
    }

    /**
     * @throws Exception
     */
    private function processFile()
    {
        $file = $this->getFile();
        $rows = $this->getFileRowsData($file);
        $skus = $this->getProductSkuWithExpectedCount($rows);
        $this->writeRevisionProducts($skus, $rows);
    }

    /**
     * @throws Exception
     */
    private function getFile(): Spreadsheet
    {
        $path = storage_path("app/public/{$this->revisionFile->uploaded_file_path}");
        $reader = IOFactory::createReaderForFile($path);
        return $reader->load($path);
    }

    /**
     * @throws Exception
     */
    private function getFileRowsData(Spreadsheet $spreadsheet): Collection
    {
        $spreadsheet = $this->getFile();
        $activeSheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($activeSheet->getRowIterator(2) as $row) {
            $rowData = [
                'product_sku_id' => $activeSheet->getCell('A' . $row->getRowIndex())->getValue(),
                'actual_count' => $activeSheet->getCell('E' . $row->getRowIndex())->getValue() ?? 0
            ];

            $rows[] = $rowData;
        }
        return collect($rows);
    }

    private function getProductSkuWithExpectedCount(Collection $rows)
    {
        $skuIds = $rows->pluck('product_sku_id')->toArray();
        return ProductSku::query()
            ->withCount(['batches as total_quantity' => function ($query) {
                $query->select(DB::raw("SUM(quantity)"))
                    ->where('store_id', $this->revisionFile->revision->store_id);
            }])
            ->whereIn('id', $skuIds)
            ->get();
    }

    private function writeRevisionProducts($skus, $excelData)
    {
        $revision = $this->revisionFile->revision;
        collect($skus)
            ->each(function ($sku) use ($excelData, $revision) {
                $item = $excelData->where('product_sku_id', $sku->id)->first();
                $revision->products()->create([
                    'product_sku_id' => $sku->id,
                    'product_id' => $sku->product_id,
                    'count_expected' => $sku->total_quantity,
                    'count_actual' => $item['actual_count']
                ]);
            });
    }
}
