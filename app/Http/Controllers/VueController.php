<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VueController extends Controller
{
    public function index() {
        \Debugbar::enable();
        return view('index');
    }
}
