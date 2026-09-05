<?php

namespace Tests\Unit\Http\Controllers\Api\V3;

use App\DTO\Filters\ClientFilterDTO;
use App\Http\Controllers\api\v3\ClientController;
use App\Repository\ClientRepository;
use App\Services\Clients\ClientExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientControllerTest extends TestCase
{
    public function testExportUsesTheSharedFilteredQueryWithoutPagination(): void
    {
        $query = $this->createMock(Builder::class);
        $repository = $this->createMock(ClientRepository::class);
        $repository->expects($this->once())
            ->method('queryForExport')
            ->with($this->callback(function (ClientFilterDTO $filters) {
                return (int) $filters->loyalty_id === 2
                    && $filters->is_partner === false;
            }))
            ->willReturn($query);

        $expectedResponse = new StreamedResponse(function () {
        });
        $service = $this->createMock(ClientExportService::class);
        $service->expects($this->once())
            ->method('download')
            ->with($query)
            ->willReturn($expectedResponse);

        $request = Request::create('/api/v3/clients/export', 'GET', [
            'loyalty_id' => 2,
            'is_partner' => 'false',
            'page' => 9,
            'per_page' => 50,
        ]);

        $response = (new ClientController($repository))->export($request, $service);

        $this->assertSame($expectedResponse, $response);
    }
}
