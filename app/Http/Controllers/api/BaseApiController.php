<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Concerns\ReturnsJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    use ReturnsJsonResponse;
}
