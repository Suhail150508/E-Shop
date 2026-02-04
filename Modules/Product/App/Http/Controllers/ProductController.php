<?php

namespace Modules\Product\App\Http\Controllers;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return view('product::index');
    }
}
