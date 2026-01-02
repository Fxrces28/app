@extends('layouts.app')

@section('title', $meditation->title)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('meditations.index') }}">Медитации</a></li>
            <li class="breadcrumb-item active">{{ $meditation->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                @if($meditation->image_path)
                    <img src="{{ asset('storage/' . $meditation->image_path) }}" 
                         class="card-img-top" alt="{{ $meditation->title }}" 
                         style="height: 300px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="card-title">{{ $meditation->title }}</h1>
                            <div class="mb-2">
                                @if($meditation->is_premium)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-crown"></i> Премиум
                                    </span>
                                @else
                                    <span class="badge bg-success">Бесплатно</span>
                                @endif
                                <span class="badge bg-info">{{ $meditation->category->name ?? '' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted">
                                <i class="fas fa-clock"></i> {{ gmdate('i:s', $meditation->duration) }}
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-play"></i> {{ $meditation->play_count }} прослушиваний
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <i class="fas fa-music"></i> Аудиоплеер
                            </div>
                            <div class="card-body">
                                <audio controls class="w-100" id="meditation-player">
                                    <source src="{{ asset('storage/' . $meditation->audio_path) }}" type="audio/mpeg">
                                    Ваш браузер не поддерживает аудио элементы.
                                </audio>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Описание</h5>
                        <div class="description-content">
                            {!! $meditation->description !!}
                        </div>
                    </div>

                    @if($meditation->tags->isNotEmpty())
                    <div class="mb-4">
                        <h5>Теги</h5>
                        <div>
                            @foreach($meditation->tags as $tag)
                                <span class="badge bg-light text-dark border me-1">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($meditation->is_premium)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-crown"></i> Премиум контент
                    </div>
                    <div class="card-body">
                        @auth
                            @if(auth()->user()->hasActiveSubscription())
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> У вас есть активная подписка
                                    @php
                                        $subscriptionEndsAt = auth()->user()->getSubscriptionEndsAt();
                                    @endphp
                                    @if($subscriptionEndsAt)
                                        <div class="mt-2">
                                            <small>
                                                Подписка действует до: 
                                                <strong>{{ $subscriptionEndsAt->format('d.m.Y H:i') }}</strong>
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <h6>Для доступа к этой медитации нужна подписка</h6>
                                    <p>Приобретите подписку, чтобы открыть все премиум медитации</p>
                                    <a href="{{ route('subscriptions.plans') }}" class="btn btn-warning w-100">
                                        <i class="fas fa-crown"></i> Выбрать тариф
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <h6>Требуется авторизация</h6>
                                <p>Для доступа к премиум контенту необходимо:</p>
                                <ol class="mb-3">
                                    <li>Зарегистрироваться</li>
                                    <li>Приобрести подписку</li>
                                </ol>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Войти
                                    </a>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus"></i> Регистрация
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Информация</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-calendar me-2 text-muted"></i>
                            Добавлена: {{ $meditation->created_at->format('d.m.Y') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-list me-2 text-muted"></i>
                            Категория: {{ $meditation->category->name ?? 'Не указана' }}
                        </li>
                        <li>
                            <i class="fas fa-play-circle me-2 text-muted"></i>
                            Прослушиваний: {{ $meditation->play_count }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('styles')
<style>
.description-content {
    line-height: 1.6;
}
.description-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
.description-content h1, 
.description-content h2, 
.description-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.description-content p {
    margin-bottom: 1rem;
}
.description-content ul, 
.description-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
</style>
@endsection