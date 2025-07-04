<?php

namespace App\Services;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WithdrawalService
{
    protected $flutterwaveSecretKey;
    protected $flutterwavePublicKey;

    public function __construct()
    {
        $this->flutterwaveSecretKey = config('services.flutterwave.secret_key');
        $this->flutterwavePublicKey = config('services.flutterwave.public_key');
    }

    public function initiateWithdrawal(User $user, float $amount)
    {
        // Check if user has sufficient balance
        if ($user->wallet->balance < $amount) {
            return [
                'success' => false,
                'message' => 'Insufficient balance',
            ];
        }

        // Check if user has bank details
        if (!$user->wallet->bank_account_number || !$user->wallet->bank_name || !$user->wallet->bank_account_name) {
            return [
                'success' => false,
                'message' => 'Bank account details not found',
            ];
        }

        try {
            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            // Initiate Flutterwave transfer
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->flutterwaveSecretKey}",
            ])->post('https://api.flutterwave.com/v3/transfers', [
                'account_bank' => $user->wallet->bank_name,
                'account_number' => $user->wallet->bank_account_number,
                'amount' => $amount,
                'narration' => "Withdrawal from Pelevo Podcast",
                'currency' => 'NGN',
                'reference' => "PELEVO-{$withdrawal->id}",
                'beneficiary_name' => $user->wallet->bank_account_name,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Update withdrawal record
                $withdrawal->update([
                    'flutterwave_reference' => $data['data']['reference'],
                    'status' => 'processing',
                ]);

                // Deduct from user's wallet
                $user->wallet->update([
                    'balance' => $user->wallet->balance - $amount,
                ]);

                return [
                    'success' => true,
                    'message' => 'Withdrawal initiated successfully',
                    'data' => $withdrawal,
                ];
            }

            // Update withdrawal record with failure reason
            $withdrawal->update([
                'status' => 'failed',
                'failure_reason' => $response->json()['message'] ?? 'Unknown error',
            ]);

            return [
                'success' => false,
                'message' => 'Failed to initiate withdrawal',
                'error' => $response->json()['message'] ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            Log::error('Withdrawal Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'An error occurred while processing withdrawal',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function checkWithdrawalStatus(Withdrawal $withdrawal)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->flutterwaveSecretKey}",
            ])->get("https://api.flutterwave.com/v3/transfers/{$withdrawal->flutterwave_reference}");

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['data']['status'];

                $withdrawal->update([
                    'status' => $status === 'SUCCESSFUL' ? 'completed' : ($status === 'FAILED' ? 'failed' : 'processing'),
                    'failure_reason' => $status === 'FAILED' ? ($data['data']['complete_message'] ?? 'Transfer failed') : null,
                ]);

                return [
                    'success' => true,
                    'status' => $withdrawal->status,
                    'message' => $withdrawal->failure_reason,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check withdrawal status',
            ];
        } catch (\Exception $e) {
            Log::error('Withdrawal Status Check Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'An error occurred while checking withdrawal status',
                'error' => $e->getMessage(),
            ];
        }
    }
} 