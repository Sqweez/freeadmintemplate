<?php

namespace App\DTO\Reports;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    public static function fromRequest(Request $request): self {
        $data = static::mapDataFromRequest($request);
        return new static($data);
    }

    protected static function mapDataFromRequest(Request $request): array {
        return $request->all();
    }
}
