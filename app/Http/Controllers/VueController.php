<?php

namespace App\Http\Controllers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;

class VueController extends Controller
{
    public function index() {
        return view('index');
    }
}
