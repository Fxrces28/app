<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Services\YookassaService;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $yookassaService;
    
    public function __construct()
    {
        $this->yookassaService = new YookassaService();
        $this->middleware('auth');
    }
    
    public function checkout(SubscriptionPlan $plan)
    {
        return view('payments.checkout', compact('plan'));
    }
    
    public function create(Request $request, $planId)
    {
        try {
            $user = auth()->user();
            $plan = SubscriptionPlan::findOrFail($planId);
            
            Log::info('Creating payment request', [
                'user_id' => $user->id,
                'plan_id' => $planId,
                'price' => $plan->price
            ]);
            
            $result = $this->yookassaService->createPayment($user, $plan, [
                'payment_method' => $request->input('payment_method', 'bank_card')
            ]);
            
            if ($result['success'] && !empty($result['confirmation_url'])) {
                Log::info('Redirecting to Yookassa:', ['url' => $result['confirmation_url']]);
                
                session(['last_payment_id' => $result['payment']->id]);
                
                return redirect()->away($result['confirmation_url']);
            }
            
            $error = $result['error'] ?? 'Неизвестная ошибка создания платежа';
            Log::error('Payment creation failed: ' . $error);
            
            return back()->withErrors(['error' => $error]);

        } catch (\Exception $e) {
            Log::error('Payment creation error: ' . $e->getMessage());
            
            return back()->withErrors([
                'error' => 'Произошла ошибка: ' . $e->getMessage()
            ]);
        }
    }
    
    public function success(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');
            $message = 'Оплата успешно завершена!';
            
            Log::info('Success page accessed', [
                'payment_id_param' => $paymentId,
                'all_params' => $request->all()
            ]);
            
            $payment = null;
            
            if ($paymentId) {
                $payment = Payment::where('yookassa_payment_id', $paymentId)->first();
                
                if (!$payment) {
                    $paymentInfo = $this->yookassaService->getPaymentInfo($paymentId);
                    if ($paymentInfo && isset($paymentInfo['metadata']['payment_id'])) {
                        $payment = Payment::find($paymentInfo['metadata']['payment_id']);
                    }
                }
            }
            
            if (!$payment && session()->has('last_payment_id')) {
                $payment = Payment::find(session('last_payment_id'));
            }
            
            if ($payment) {
                $plan = $payment->plan;
                
                if ($payment->status === 'pending') {
                    $paymentInfo = $this->yookassaService->getPaymentInfo($payment->yookassa_payment_id);
                    if ($paymentInfo && $paymentInfo['status'] === 'succeeded') {
                        $payment->update(['status' => 'succeeded']);
                    }
                }
                
                session()->forget('last_payment_id');
                
                return view('payments.success', compact('plan', 'message'));
            }
            
            return view('payments.success', compact('message'));
            
        } catch (\Exception $e) {
            Log::error('Success page error: ' . $e->getMessage());
            return view('payments.success', ['message' => 'Спасибо за оплату!']);
        }
    }
    
    public function cancel(Request $request)
    {
        $message = $request->get('message', 'Оплата была отменена. Вы можете попробовать снова.');
        
        session()->forget('last_payment_id');
        
        return view('payments.cancel', compact('message'));
    }
    
    public function testCreate(Request $request, $planId)
    {
        try {
            $user = auth()->user();
            $plan = SubscriptionPlan::findOrFail($planId);
            
            Log::info('Test payment requested', [
                'user' => $user->id,
                'plan' => $plan->id
            ]);
            
            return $this->create($request, $planId);
            
        } catch (\Exception $e) {
            Log::error('Test payment error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    public function webhook(Request $request)
    {
        Log::info('=== WEBHOOK RECEIVED ===');
        Log::info('Headers:', $request->headers->all());
        Log::info('Body:', $request->all());
        
        $data = $request->all();
        
        $result = $this->yookassaService->handleWebhook($data);
        
        if ($result) {
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 400);
    }
}