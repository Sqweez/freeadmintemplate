<?php

namespace Tests\Unit\DTO\Filters;

use App\DTO\Filters\ClientFilterDTO;
use PHPUnit\Framework\TestCase;

class ClientFilterDTOTest extends TestCase
{
    public function testBooleanFiltersAreNullWhenTheyAreNotProvided(): void
    {
        $filters = new ClientFilterDTO([]);

        $this->assertNull($filters->is_partner);
        $this->assertNull($filters->is_wholesale_buyer);
        $this->assertNull($filters->is_kaspi);
        $this->assertNull($filters->without_code);
    }

    /**
     * @dataProvider booleanValuesProvider
     */
    public function testBooleanFiltersAreParsed($value, bool $expected): void
    {
        $filters = new ClientFilterDTO([
            'is_partner' => $value,
            'is_wholesale_buyer' => $value,
            'is_kaspi' => $value,
            'without_code' => $value,
        ]);

        $this->assertSame($expected, $filters->is_partner);
        $this->assertSame($expected, $filters->is_wholesale_buyer);
        $this->assertSame($expected, $filters->is_kaspi);
        $this->assertSame($expected, $filters->without_code);
    }

    public function booleanValuesProvider(): array
    {
        return [
            'string true' => ['true', true],
            'boolean true' => [true, true],
            'string one' => ['1', true],
            'integer one' => [1, true],
            'string false' => ['false', false],
            'boolean false' => [false, false],
            'string zero' => ['0', false],
            'integer zero' => [0, false],
        ];
    }
}
