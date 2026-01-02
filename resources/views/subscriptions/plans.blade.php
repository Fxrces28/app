@extends('layouts.app')

@section('title', 'Тарифные планы')

@section('content')
<div class="container">
    <h1 class="text-center mb-5">Выберите подходящий тариф</h1>
    
    <div class="row justify-content-center">
        @foreach($plans as $plan)
        <div class="col-md-4 mb-4">
            <div class="card h-100 plan-card {{ $plan->is_popular ? 'popular' : '' }}">
                @if($plan->is_popular)
                    <div class="card-header text-center bg-warning text-dark">
                        <strong><i class="fas fa-crown"></i> ПОПУЛЯРНЫЙ ВЫБОР</strong>
                    </div>
                @endif
                
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $plan->name }}</h3>
                    <div class="price display-3 fw-bold my-3">
                        @if($plan->price == 0)
                            Бесплатно
                        @else
                            {{ number_format($plan->price, 0) }} ₽
                        @endif
                    </div>
                    <div class="text-muted mb-3">
                        @switch($plan->duration)
                            @case('month')
                                в месяц
                                @break
                            @case('6months')
                                на 6 месяцев
                                @break
                            @case('year')
                                в год
                                @break
                            @case('lifetime')
                                пожизненно
                                @break
                        @endswitch
                    </div>
                    
                    <p>{{ $plan->description }}</p>
                    
                    @if($plan->features)
                        <div class="features text-start mt-4">
                            <h6>Включено:</h6>
                            <ul class="list-unstyled">
                                @foreach(json_decode($plan->features, true) as $feature)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    @if($plan->price == 0)
                        <form method="POST" action="{{ route('subscriptions.activate-free') }}" class="w-100">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        </form>
                    @else
                        <a href="{{ route('payment.checkout', $plan) }}" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart"></i> Купить
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('styles')
<style>
.plan-card {
    transition: transform 0.3s;
}
.plan-card:hover {
    transform: translateY(-5px);
}
.plan-card.popular {
    box-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
}
.price {
    color: #667eea;
}
.features li {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}
.features li:last-child {
    border-bottom: none;
}
</style>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.choose-plan').forEach(button => {
    button.addEventListener('click', function() {
        const planId = this.getAttribute('data-plan-id');
        alert('Вы выбрали тариф. В реальном приложении здесь будет переход к оплате.\nID тарифа: ' + planId);
    });
});
</script>
@endsection