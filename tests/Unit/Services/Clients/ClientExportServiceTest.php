<?php

namespace Tests\Unit\Services\Clients;

use App\Client;
use App\Services\Clients\ClientExportService;
use App\v2\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class ClientExportServiceTest extends TestCase
{
    public function testItStreamsAFormattedClientWorkbook(): void
    {
        $clients = new Collection([
            $this->client('Анна', 'Алматы', 125000, '+77001234567', 1),
            $this->client('Иван', 'Город не указан', 0, '87007654321', 2),
        ]);

        $query = $this->createMock(Builder::class);
        $query->expects($this->once())
            ->method('chunkById')
            ->with(500, $this->callback(function (callable $callback) use ($clients) {
                $callback($clients);
                return true;
            }))
            ->willReturn(true);

        $response = (new ClientExportService())->download($query);

        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
        $this->assertStringContainsString('clients_', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));

        ob_start();
        $response->sendContent();
        $contents = ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'clients-export-');
        file_put_contents($path, $contents);
        $spreadsheet = IOFactory::load($path);
        unlink($path);

        $sheet = $spreadsheet->getActiveSheet();
        $this->assertSame('Клиенты', $sheet->getTitle());
        $this->assertSame(
            ['Имя', 'Город', 'Сумма покупок за всё время', 'Телефон'],
            $sheet->rangeToArray('A1:D1')[0]
        );
        $this->assertSame('Анна', $sheet->getCell('A2')->getValue());
        $this->assertSame('Алматы', $sheet->getCell('B2')->getValue());
        $this->assertSame(125000, $sheet->getCell('C2')->getValue());
        $this->assertSame('+77001234567', $sheet->getCell('D2')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D2')->getDataType());
        $this->assertSame('Город не указан', $sheet->getCell('B3')->getValue());
        $this->assertSame('A1:D3', $sheet->getAutoFilter()->getRange());
        $this->assertSame('A2', $sheet->getFreezePane());
        $this->assertTrue($sheet->getStyle('A1')->getFont()->getBold());
        $this->assertSame('#,##0.00 [$₸-kk-KZ]', $sheet->getStyle('C2')->getNumberFormat()->getFormatCode());

        $spreadsheet->disconnectWorksheets();
    }

    public function testItExportsAValidHeaderOnlyWorkbookWhenNoClientsMatch(): void
    {
        $query = $this->createMock(Builder::class);
        $query->expects($this->once())
            ->method('chunkById')
            ->with(500, $this->isType('callable'))
            ->willReturn(true);

        $response = (new ClientExportService())->download($query);

        ob_start();
        $response->sendContent();
        $contents = ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'clients-export-empty-');
        file_put_contents($path, $contents);
        $spreadsheet = IOFactory::load($path);
        unlink($path);

        $sheet = $spreadsheet->getActiveSheet();
        $this->assertSame('A1:D1', $sheet->getAutoFilter()->getRange());
        $this->assertSame('Имя', $sheet->getCell('A1')->getValue());
        $this->assertNull($sheet->getCell('A2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testItStoresFormulaLikeClientValuesAsPlainText(): void
    {
        $clients = new Collection([
            $this->client('=1+1', '=2+2', 100, '+77000000000', 1),
        ]);
        $query = $this->createMock(Builder::class);
        $query->method('chunkById')->willReturnCallback(function ($size, $callback) use ($clients) {
            $callback($clients);
            return true;
        });

        $response = (new ClientExportService())->download($query);
        ob_start();
        $response->sendContent();
        $contents = ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'clients-export-text-');
        file_put_contents($path, $contents);
        $spreadsheet = IOFactory::load($path);
        unlink($path);

        $sheet = $spreadsheet->getActiveSheet();
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A2')->getDataType());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('B2')->getDataType());
        $this->assertSame('=1+1', $sheet->getCell('A2')->getValue());
        $this->assertSame('=2+2', $sheet->getCell('B2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    private function client(string $name, string $cityName, int $amount, string $phone, int $id): Client
    {
        $client = new Client([
            'client_name' => $name,
            'client_phone' => $phone,
            'client_city' => $id,
            'cached_total_sale_amount' => $amount,
        ]);
        $client->id = $id;
        $client->setRelation('city', new City(['name' => $cityName]));

        return $client;
    }
}
