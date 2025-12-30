@extends('layouts.app')

@section('title', 'Оплата успешна')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle"></i> Оплата успешна!</h4>
                </div>
                
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                        <h3>Спасибо за покупку!</h3>
                        <p class="lead">{{ $message ?? 'Ваша подписка успешно активирована.' }}</p>
                    </div>
                    
                    @if(isset($plan))
                    <div class="alert alert-info text-start">
                        <h5>Информация о подписке:</h5>
                        <ul class="mb-0">
                            <li><strong>Тариф:</strong> {{ $plan->name }}</li>
                            <li><strong>Стоимость:</strong> {{ number_format($plan->price, 0) }} ₽</li>
                            <li><strong>Длительность:</strong> 
                                @switch($plan->duration)
                                    @case('month') 1 месяц @break
                                    @case('3months') 3 месяца @break
                                    @case('year') 1 год @break
                                    @case('lifetime') пожизненно @break
                                @endswitch
                            </li>
                        </ul>
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary me-2">
                            На главную
                        </a>
                        <a href="{{ route('meditations.index') }}" class="btn btn-success">
                            Начать слушать
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection