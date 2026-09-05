<?php

namespace Tests\Unit\Repository;

use App\DTO\Filters\ClientFilterDTO;
use App\Repository\ClientRepository;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $database = new Capsule();
        $database->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $database->setAsGlobal();
        $database->bootEloquent();
    }

    /**
     * @dataProvider filterProvider
     */
    public function testQueryAppliesClientFilters(array $payload, string $column, $binding): void
    {
        $query = (new ClientRepository())->query(new ClientFilterDTO($payload));

        $this->assertStringContainsString($column, $query->toSql());
        $this->assertContains($binding, $query->getBindings());
    }

    public function filterProvider(): array
    {
        return [
            'loyalty' => [['loyalty_id' => 2], 'loyalty_id', 2],
            'gender' => [['gender' => 'F'], 'gender', 'F'],
            'city' => [['client_city' => -1], 'client_city', -1],
            'regular client' => [['is_partner' => 'false'], 'is_partner', false],
            'partner' => [['is_partner' => 'true'], 'is_partner', true],
            'wholesale buyer' => [['is_wholesale_buyer' => 'true'], 'is_wholesale_buyer', true],
            'kaspi client' => [['is_kaspi' => 'true'], 'is_kaspi', true],
        ];
    }

    public function testQueryAppliesSearchToNameCardAndPhone(): void
    {
        $query = (new ClientRepository())->query(new ClientFilterDTO(['search' => 'Alice']));

        $this->assertStringContainsString('client_name', $query->toSql());
        $this->assertStringContainsString('client_card', $query->toSql());
        $this->assertStringContainsString('client_phone', $query->toSql());
        $this->assertSame(['%Alice%', 'Alice', '%Alice%'], $query->getBindings());
    }

    public function testQueryAppliesWithoutCodeFilter(): void
    {
        $query = (new ClientRepository())->query(new ClientFilterDTO(['without_code' => 'true']));

        $this->assertStringContainsString('client_card" is null', $query->toSql());
        $this->assertStringContainsString('LENGTH(client_card) < 5', $query->toSql());
    }

    public function testQueryDoesNotApplyAbsentBooleanFilters(): void
    {
        $sql = (new ClientRepository())->query(new ClientFilterDTO([]))->toSql();

        $this->assertStringNotContainsString('"is_partner" = ?', $sql);
        $this->assertStringNotContainsString('"is_wholesale_buyer" = ?', $sql);
        $this->assertStringNotContainsString('"is_kaspi" = ?', $sql);
        $this->assertStringNotContainsString('LENGTH(client_card)', $sql);
    }

    public function testExportQueryUsesTheSameFiltersAndOnlyRequiredColumns(): void
    {
        $filters = new ClientFilterDTO([
            'search' => 'Alice',
            'client_city' => 3,
            'is_partner' => 'false',
        ]);
        $repository = new ClientRepository();

        $listQuery = $repository->query($filters);
        $exportQuery = $repository->queryForExport($filters);

        $listWhereSql = explode(' order by ', explode(' where ', $listQuery->toSql(), 2)[1], 2)[0];
        $exportWhereSql = explode(' where ', $exportQuery->toSql(), 2)[1];

        $this->assertSame($listWhereSql, $exportWhereSql);
        $this->assertSame($listQuery->getBindings(), $exportQuery->getBindings());
        $this->assertSame(
            ['id', 'client_name', 'client_phone', 'client_city', 'cached_total_sale_amount'],
            $exportQuery->getQuery()->columns
        );
        $this->assertArrayHasKey('city', $exportQuery->getEagerLoads());
        $this->assertArrayNotHasKey('loyalty', $exportQuery->getEagerLoads());
    }

    public function testExportQueryDoesNotKeepListOrderingBeforeChunkingById(): void
    {
        $repository = new ClientRepository();

        $listQuery = $repository->query(new ClientFilterDTO([]));
        $exportQuery = $repository->queryForExport(new ClientFilterDTO([]));

        $this->assertSame(
            [['column' => 'created_at', 'direction' => 'desc']],
            $listQuery->getQuery()->orders
        );
        $this->assertNull($exportQuery->getQuery()->orders);
    }
}
