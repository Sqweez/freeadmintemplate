<?php

namespace App\Http\Controllers;

class VueController extends Controller
{
    public function index() {
        return view('index');
    }

    public function fit()
    {
        return view('fit');
    }
}
