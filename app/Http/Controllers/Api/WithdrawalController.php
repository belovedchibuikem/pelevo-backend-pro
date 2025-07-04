<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    protected $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $result = $this->withdrawalService->initiateWithdrawal($request->user(), $request->amount);
        return response()->json($result);
    }

    public function index(Request $request)
    {
        $withdrawals = $request->user()->withdrawals()->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($withdrawals);
    }

    public function status(Request $request, Withdrawal $withdrawal)
    {
        $result = $this->withdrawalService->checkWithdrawalStatus($withdrawal);
        return response()->json($result);
    }
} 