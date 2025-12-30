@extends('layouts.app')

@section('title', 'Оплата отменена')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center border-warning">
                <div class="card-header bg-warning">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Оплата отменена</h4>
                </div>
                
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle fa-5x text-warning mb-4"></i>
                        <h3>Оплата не завершена</h3>
                        <p class="lead">{{ $message ?? 'Вы отменили процесс оплаты.' }}</p>
                    </div>
                    
                    <div class="alert alert-light text-start">
                        <h5>Что можно сделать:</h5>
                        <ul class="mb-0">
                            <li>Попробовать оплатить снова</li>
                            <li>Выбрать другой тариф</li>
                            <li>Обратиться в поддержку</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('subscriptions.plans') }}" class="btn btn-primary me-2">
                            <i class="fas fa-crown"></i> Выбрать другой тариф
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> На главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection