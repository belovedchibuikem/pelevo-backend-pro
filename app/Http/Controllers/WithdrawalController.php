<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        return view('withdrawals.index');
    }
} 