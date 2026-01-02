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
                    <div class="mb-4">
                        <h1 class="card-title">{{ $meditation->title }}</h1>
                        <h5>Описание</h5>
                        <div class="description-content">
                            {!! $meditation->description !!}
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
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Информация</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-tag me-2 text-muted"></i>
                            Статус: 
                            @if($meditation->is_premium)
                                <span class="badge bg-warning">
                                    <i class="fas fa-crown"></i> Премиум
                                </span>
                            @else
                                <span class="badge bg-success">Бесплатно</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2 text-muted"></i>
                            Длительность: {{ gmdate('i:s', $meditation->duration) }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-play-circle me-2 text-muted"></i>
                            Прослушиваний: {{ $meditation->play_count }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar me-2 text-muted"></i>
                            Добавлена: {{ $meditation->created_at->format('d.m.Y') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-list me-2 text-muted"></i>
                            Категория: {{ $meditation->category->name ?? 'Не указана' }}
                        </li>
                        <br>
<div class="mt-2">
    @auth
        @if(auth()->user()->hasFavorite($meditation))
            <button class="btn btn-sm btn-danger remove-favorite-btn" 
                    data-meditation-id="{{ $meditation->id }}"
                    data-is-favorite="true"
                    title="Удалить из избранного">
                <i class="fas fa-heart"></i>
                <span class="ms-1">В избранном</span>
            </button>
        @else
            <button class="btn btn-sm btn-outline-danger add-favorite-btn" 
                    data-meditation-id="{{ $meditation->id }}"
                    data-is-favorite="false"
                    title="Добавить в избранное">
                <i class="far fa-heart"></i>
                <span class="ms-1">В избранное</span>
            </button>
        @endif
    @else
        <button class="btn btn-sm btn-outline-danger" 
                title="Для добавления в избранное требуется авторизация"
                onclick="window.location.href='{{ route('login') }}'">
            <i class="far fa-heart"></i>
            <span class="ms-1">В избранное</span>
        </button>
    @endauth
</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoritesToggleUrl = "{{ route('favorites.toggle') }}";
    const csrfToken = "{{ csrf_token() }}";
    
    console.log('Favorites script loaded!');
    
    function updateButtonState(button, isFavorite) {
        if (isFavorite) {
            button.classList.remove('btn-outline-danger', 'add-favorite-btn');
            button.classList.add('btn-danger', 'remove-favorite-btn');
            button.innerHTML = '<i class="fas fa-heart"></i> <span class="ms-1">В избранном</span>';
            button.setAttribute('title', 'Удалить из избранного');
            button.setAttribute('data-is-favorite', 'true');
        } else {
            button.classList.remove('btn-danger', 'remove-favorite-btn');
            button.classList.add('btn-outline-danger', 'add-favorite-btn');
            button.innerHTML = '<i class="far fa-heart"></i> <span class="ms-1">В избранное</span>';
            button.setAttribute('title', 'Добавить в избранное');
            button.setAttribute('data-is-favorite', 'false');
        }
        button.disabled = false;
    }
    
    @auth
    fetch("{{ route('favorites.list') }}")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Server favorites:', data);
            if (data.success) {
                document.querySelectorAll('[data-meditation-id]').forEach(button => {
                    const meditationId = button.getAttribute('data-meditation-id');
                    const isFavorite = data.favorites.includes(parseInt(meditationId));
                    updateButtonState(button, isFavorite);
                });
            }
        })
        .catch(error => console.error('Error loading favorites:', error));
    @endauth
    
    document.addEventListener('click', async function(e) {
        let button;
        
        if (e.target.closest('.add-favorite-btn')) {
            button = e.target.closest('.add-favorite-btn');
        } else if (e.target.closest('.remove-favorite-btn')) {
            button = e.target.closest('.remove-favorite-btn');
        } else if (e.target.closest('.favorite-btn')) {
            button = e.target.closest('.favorite-btn');
        }
        
        if (button) {
            e.preventDefault();
            e.stopPropagation();
            
            const meditationId = button.getAttribute('data-meditation-id');
            const isOnFavoritesPage = window.location.pathname.includes('/favorites');
            
            console.log('Toggle favorite:', meditationId);
            
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
            
            try {
                const response = await fetch(favoritesToggleUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        meditation_id: meditationId
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Response:', data);
                
                if (data.success) {
                    updateButtonState(button, data.is_favorite);
                    
                    if (isOnFavoritesPage && !data.is_favorite) {
                        const card = button.closest('.col-md-4');
                        if (card) {
                            card.remove();
                            
                            if (document.querySelectorAll('.col-md-4').length === 0) {
                                location.reload();
                            }
                        }
                    }
                } else {
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                button.innerHTML = originalHtml;
                button.disabled = false;
            }
        }
    });
});
</script>