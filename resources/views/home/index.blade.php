@extends('layouts.app')

@section('title', 'Улучшение сна и ментального здоровья')

@section('content')
<div class="container">
    <div class="hero-section text-center py-5 mb-5 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container py-5">
            <h1 class="display-4 text-white fw-bold mb-4">
                <i class="fas fa-moon"></i> Твое состояние
            </h1>
            <p class="lead text-white mb-4" style="font-size: 1.5rem;">
                Ваш персональный помощник для качественного сна и ментального здоровья
            </p>
            <div class="mt-4">
                <a href="{{ route('meditations.index') }}" class="btn btn-light btn-lg me-2">
                    <i class="fas fa-headphones"></i> Начать медитацию
                </a>
                <a href="{{ route('subscriptions.plans') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-crown"></i> Выбрать тариф
                </a>
            </div>
        </div>
    </div>

    <h2 class="text-center mb-4">Популярные медитации</h2>
    <div class="row g-4 mb-5">
        @php
            $popularMeditations = \App\Models\Meditation::orderBy('play_count', 'desc')
                ->limit(6)
                ->get();
        @endphp
        
        @foreach($popularMeditations as $meditation)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                @if($meditation->image_path)
                    <img src="{{ asset('storage/' . $meditation->image_path) }}" 
                         class="card-img-top" alt="{{ $meditation->title }}" style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="fas fa-music fa-3x text-white"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $meditation->title }}</h5>
                    <p class="text-muted">{{ strip_tags($meditation->description) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> {{ gmdate('i:s', $meditation->duration) }}
                        </small>
                        @if($meditation->is_premium)
                            <span class="badge bg-warning">
                                <i class="fas fa-crown"></i> Премиум
                            </span>
                        @else
                            <span class="badge bg-success">Бесплатно</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('meditations.show', $meditation) }}" class="btn btn-primary w-100">
                        <i class="fas fa-play"></i> Слушать
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <h2 class="text-center mb-4">Наши тарифы</h2>
    <div class="row g-4 mb-5">
        @php
            $plans = \App\Models\SubscriptionPlan::where('is_active', true)
                ->orderBy('sort_order')
                ->limit(3)
                ->get();
        @endphp
        
        @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card h-100 {{ $plan->is_popular ? 'border-warning border-2' : '' }}">
                @if($plan->is_popular)
                    <div class="card-header bg-warning text-center">
                        <strong>Популярный выбор</strong>
                    </div>
                @endif
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $plan->name }}</h3>
                    <div class="display-4 fw-bold my-3">
                        @if($plan->price == 0)
                            Бесплатно
                        @else
                            {{ number_format($plan->price, 0) }} ₽
                        @endif
                    </div>
                    <p class="text-muted">{{ $plan->description }}</p>
                    
                    @if($plan->features)
                        <ul class="list-unstyled text-start">
                            @foreach(json_decode($plan->features, true) as $feature)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('subscriptions.plans') }}" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i> Выбрать
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection