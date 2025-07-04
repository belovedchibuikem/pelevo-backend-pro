<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function index()
    {
        return view('earnings.index');
    }
} 