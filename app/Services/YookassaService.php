<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class YookassaService
{
    private $shopId;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $mode = config('yookassa.mode', 'sandbox');
        
        if ($mode === 'sandbox') {
            $this->shopId = config('yookassa.sandbox.shop_id');
            $this->secretKey = config('yookassa.sandbox.secret_key');
            $this->baseUrl = 'https://api.yookassa.ru/v3/';
        } else {
            $this->shopId = config('yookassa.production.shop_id');
            $this->secretKey = config('yookassa.production.secret_key');
            $this->baseUrl = 'https://api.yookassa.ru/v3/';
        }
        
        Log::info('YookassaService initialized', [
            'mode' => $mode,
            'shop_id' => substr($this->shopId, 0, 3) . '...',
            'base_url' => $this->baseUrl
        ]);
    }

    public function createPayment(User $user, SubscriptionPlan $plan, array $options = [])
    {
        try {
            Log::info('=== START CREATING PAYMENT ===');
            Log::info('User: ' . $user->id . ', Plan: ' . $plan->id . ', Price: ' . $plan->price);

            $subscriptionEndsAt = match($plan->duration) {
                        'month' => now()->addMonth(),
                        '3months' => now()->addMonths(3),
                        'year' => now()->addYear(),
                        'lifetime' => now()->addYears(100),
                        default => now()->addMonth(),
                    };

            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => 'RUB',
                'description' => "Подписка: {$plan->name}",
                'status' => 'pending',
                'metadata' => [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'plan_duration' => $plan->duration,
                ],
                'expires_at' => now()->addHours(24),
                'subscription_ends_at' => $subscriptionEndsAt,
            ]);

            Log::info('Payment created in DB: ' . $payment->id);

            $paymentData = [
                'amount' => [
                    'value' => number_format($plan->price, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => url('/payment/success'),
                ],
                'capture' => true,
                'description' => "Подписка: {$plan->name}",
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ];

            if (config('yookassa.mode') !== 'sandbox') {
                $paymentData['receipt'] = [
                    'customer' => [
                        'email' => $user->email,
                    ],
                    'items' => [
                        [
                            'description' => "Подписка: {$plan->name}",
                            'quantity' => '1.00',
                            'amount' => [
                                'value' => number_format($plan->price, 2, '.', ''),
                                'currency' => 'RUB',
                            ],
                            'vat_code' => 1,
                            'payment_mode' => 'full_payment',
                            'payment_subject' => 'service',
                        ],
                    ],
                ];
            }

            Log::info('Sending request to Yookassa:', [
                'url' => $this->baseUrl . 'payments',
                'shop_id' => substr($this->shopId, 0, 3) . '...',
                'data' => $paymentData
            ]);

            $response = Http::withBasicAuth($this->shopId, $this->secretKey)
                ->timeout(30)
                ->withHeaders([
                    'Idempotence-Key' => uniqid('', true),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . 'payments', $paymentData);

            $responseBody = $response->json();
            $statusCode = $response->status();

            Log::info('Yookassa response:', [
                'status' => $statusCode,
                'successful' => $response->successful(),
                'body' => $responseBody
            ]);

            if ($response->successful()) {
                $payment->update([
                    'yookassa_payment_id' => $responseBody['id'],
                    'status' => $responseBody['status'],
                    'confirmation_url' => $responseBody['confirmation']['confirmation_url'] ?? null,
                ]);

                Log::info('Payment created successfully:', [
                    'payment_id' => $payment->id,
                    'yookassa_id' => $responseBody['id'],
                    'confirmation_url' => $responseBody['confirmation']['confirmation_url']
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'confirmation_url' => $responseBody['confirmation']['confirmation_url'],
                    'payment_id' => $responseBody['id'],
                ];
            } else {
                Log::error('Yookassa API error:', [
                    'status' => $statusCode,
                    'error' => $responseBody,
                    'request_data' => $paymentData
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Ошибка при создании платежа: ' . ($responseBody['description'] ?? 'Неизвестная ошибка'),
                    'debug' => $responseBody,
                    'status' => $statusCode,
                ];
            }

        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return [
                'success' => false,
                'error' => 'Ошибка при создании платежа: ' . $e->getMessage(),
                'debug' => $e->getMessage(),
            ];
        }
    }


    public function getPaymentInfo($paymentId)
    {
        try {
            Log::info('Getting payment info for: ' . $paymentId);
            
            $response = Http::withBasicAuth($this->shopId, $this->secretKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . 'payments/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Payment info received:', ['status' => $data['status']]);
                return $data;
            }

            Log::error('Failed to get payment info: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Get payment info failed: ' . $e->getMessage());
            return null;
        }
    }


    public function handleWebhook(array $data)
    {
        try {
            Log::info('Webhook received:', $data);
            
            $paymentId = $data['object']['id'] ?? null;
            $event = $data['event'] ?? null;
            $status = $data['object']['status'] ?? null;

            if (!$paymentId || !$event) {
                Log::warning('Invalid webhook data received');
                return false;
            }

            $payment = Payment::where('yookassa_payment_id', $paymentId)->first();
            
            if (!$payment) {
                Log::warning("Payment not found for Yookassa ID: {$paymentId}");
                return false;
            }

            $payment->update([
                'status' => $status,
                'paid_at' => $status === 'succeeded' ? now() : null,
                'captured_at' => $status === 'succeeded' ? now() : null,
            ]);

            Log::info("Payment status updated: {$paymentId} -> {$status}");

            if ($status === 'succeeded' && $event === 'payment.succeeded') {
                $this->activateSubscription($payment);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Webhook handling failed: ' . $e->getMessage());
            return false;
        }
    }

    private function activateSubscription(Payment $payment)
    {
        try {
            $user = $user = $payment->user;
            $plan = $plan = $payment->plan;

            $endsAt = $payment->subscription_ends_at ?? match($plan->duration) {
                'month' => now()->addMonth(),
                '3months' => now()->addMonths(3),
                'year' => now()->addYear(),
                'lifetime' => now()->addYears(100),
                default => now()->addMonth(),
            };

            Log::info('Activating subscription for payment: ' . $payment->id, [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'ends_at' => $endsAt
            ]);

            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'is_active' => true,
                'status' => 'active',
                'payment_id' => $payment->yookassa_payment_id,
            ]);


            $user->update([
                'subscription_active' => true,
                'subscription_ends_at' => $endsAt,
                'current_plan_id' => $plan->id,
            ]);

            Log::info("Subscription activated for user {$user->id}, plan {$plan->id}");

        } catch (\Exception $e) {
            Log::error('Subscription activation failed: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}