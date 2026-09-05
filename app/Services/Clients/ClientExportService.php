<?php

namespace App\Services\Clients;

use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientExportService
{
    private const HEADERS = [
        'Имя',
        'Город',
        'Сумма покупок за всё время',
        'Телефон',
    ];

    public function download(Builder $query): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Клиенты');
        $sheet->fromArray(self::HEADERS, null, 'A1');

        $row = 2;
        $query->chunkById(500, function ($clients) use ($sheet, &$row) {
            foreach ($clients as $client) {
                $sheet->setCellValueExplicit("A{$row}", (string) $client->client_name, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("B{$row}", (string) $client->city->name, DataType::TYPE_STRING);
                $sheet->setCellValue("C{$row}", (int) $client->cached_total_sale_amount);
                $sheet->setCellValueExplicit("D{$row}", (string) $client->client_phone, DataType::TYPE_STRING);
                $row++;
            }
        });

        $lastRow = max(1, $row - 1);
        $this->format($spreadsheet, $lastRow);

        $fileName = 'clients_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    private function format(Spreadsheet $spreadsheet, int $lastRow): void
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A1:D{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A2:D{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        if ($lastRow >= 2) {
            $sheet->getStyle("C2:C{$lastRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0.00 [$₸-kk-KZ]');
        }

        $sheet->getColumnDimension('A')->setWidth(36);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:D{$lastRow}");
    }
}
