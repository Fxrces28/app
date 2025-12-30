@extends('layouts.app')

@section('title', 'Оформление подписки')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-crown"></i> Оформление подписки</h4>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3>{{ $plan->name }}</h3>
                        <div class="display-4 fw-bold my-3 text-primary">
                            {{ number_format($plan->price, 0) }} ₽
                        </div>
                        <div class="text-muted mb-3">
                            @switch($plan->duration)
                                @case('month') на 1 месяц @break
                                @case('3months') на 3 месяца @break
                                @case('year') на 1 год @break
                                @case('lifetime') пожизненно @break
                            @endswitch
                        </div>
                    </div>

                    @if($plan->features)
                        <div class="mb-4">
                            <h5>Что входит:</h5>
                            <ul class="list-unstyled">
                                @foreach(json_decode($plan->features, true) as $feature)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('payment.create', $plan->id) }}" id="payment-form">
                        @csrf
                        
                        <div class="mb-4">
                            <h5>Способ оплаты</h5>
                            
                            <div id="payment-methods" class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                        id="bank_card" value="bank_card" checked>
                                    <label class="form-check-label" for="bank_card">
                                        <i class="fas fa-credit-card me-2"></i> Банковская карта
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="pay-btn" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i> Перейти к оплате
                            </button>
                            <small class="text-muted text-center">
                                <i class="fas fa-info-circle"></i> Будет открыта тестовая страница ЮKassa
                            </small>
                            <a href="{{ route('subscriptions.plans') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Вернуться к выбору тарифа
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const payBtn = document.getElementById('pay-btn');
    
    form.addEventListener('submit', function(e) {
        payBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Создание платежа...';
        payBtn.disabled = true;
    });
});
</script>
@endsection