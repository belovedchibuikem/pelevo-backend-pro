<?php

namespace App\Services;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;
    protected $encryptionKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.flutterwave.com/v3';
        $this->secretKey = config('services.flutterwave.secret_key');
        $this->publicKey = config('services.flutterwave.public_key');
        $this->encryptionKey = config('services.flutterwave.encryption_key');
    }

    public function initializePayment(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->post($this->baseUrl . '/payments', [
                'tx_ref' => $data['reference'],
                'amount' => $data['amount'],
                'currency' => 'NGN',
                'payment_options' => 'card,banktransfer,ussd',
                'customer' => [
                    'email' => $data['email'],
                    'name' => $data['name'],
                ],
                'customizations' => [
                    'title' => 'Pelevo Payment',
                    'description' => $data['description'],
                ],
                'redirect_url' => $data['redirect_url'],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flutterwave payment initialization failed', [
                'response' => $response->json(),
                'data' => $data,
            ]);

            throw new \Exception('Payment initialization failed');
        } catch (\Exception $e) {
            Log::error('Flutterwave payment error', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    public function verifyPayment(string $transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transactions/' . $transactionId . '/verify');

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flutterwave payment verification failed', [
                'response' => $response->json(),
                'transaction_id' => $transactionId,
            ]);

            throw new \Exception('Payment verification failed');
        } catch (\Exception $e) {
            Log::error('Flutterwave verification error', [
                'message' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            throw $e;
        }
    }

    public function processWithdrawal(Withdrawal $withdrawal)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->post($this->baseUrl . '/transfers', [
                'account_bank' => $withdrawal->bank_code,
                'account_number' => $withdrawal->account_number,
                'amount' => $withdrawal->amount,
                'narration' => 'Withdrawal from Pelevo',
                'currency' => 'NGN',
                'reference' => 'WITHDRAWAL-' . $withdrawal->id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $withdrawal->update([
                    'status' => 'processing',
                    'reference' => $data['data']['reference'],
                ]);

                return $data;
            }

            Log::error('Flutterwave withdrawal processing failed', [
                'response' => $response->json(),
                'withdrawal_id' => $withdrawal->id,
            ]);

            throw new \Exception('Withdrawal processing failed');
        } catch (\Exception $e) {
            Log::error('Flutterwave withdrawal error', [
                'message' => $e->getMessage(),
                'withdrawal_id' => $withdrawal->id,
            ]);

            throw $e;
        }
    }

    public function verifyTransfer(string $reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transfers/' . $reference);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flutterwave transfer verification failed', [
                'response' => $response->json(),
                'reference' => $reference,
            ]);

            throw new \Exception('Transfer verification failed');
        } catch (\Exception $e) {
            Log::error('Flutterwave transfer verification error', [
                'message' => $e->getMessage(),
                'reference' => $reference,
            ]);

            throw $e;
        }
    }
} 