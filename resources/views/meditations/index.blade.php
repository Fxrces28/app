@extends('layouts.app')

@section('title', 'Библиотека медитаций')

@section('content')
<div class="container">
    <h1 class="mb-4">Библиотека медитаций</h1>
    
<form method="GET" action="{{ route('meditations.index') }}" class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label for="category" class="form-label">Категория</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="type" class="form-label">Тип доступа</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Все</option>
                    <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Бесплатные</option>
                    <option value="premium" {{ request('type') == 'premium' ? 'selected' : '' }}>Премиум</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="sort" class="form-label">Сортировка</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Сначала новые</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>По популярности</option>
                    <option value="duration" {{ request('sort') == 'duration' ? 'selected' : '' }}>По длительности</option>
                </select>
            </div>
            <div class="col-md-12 mt-3">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        Применить фильтры
                    </button>
                    <a href="{{ route('meditations.index') }}" class="btn btn-outline-secondary">
                        Сбросить
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

    <div class="row g-4">
        @foreach($meditations as $meditation)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 meditation-card">
                @if($meditation->image_path)
                    <img src="{{ asset('storage/' . $meditation->image_path) }}" 
                         class="card-img-top" alt="{{ $meditation->title }}" 
                         style="height: 150px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                         style="height: 150px;">
                        <i class="fas fa-music fa-2x text-white"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h6 class="card-title">{{ $meditation->title }}</h6>
                    <p class="text-muted">{{ strip_tags($meditation->description) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> {{ gmdate('i:s', $meditation->duration) }}
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-play"></i> {{ $meditation->play_count }}
                        </small>
                    </div>
                    <div class="mt-2">
                        @if($meditation->is_premium)
                            <span class="badge bg-warning">
                                <i class="fas fa-crown"></i> Премиум
                            </span>
                        @else
                            <span class="badge bg-success">Бесплатно</span>
                        @endif
                        <span class="badge bg-info">{{ $meditation->category->name ?? '' }}</span>
                        @auth
                        <div class="mt-2">
                            @if(auth()->user()->hasFavorite($meditation))
                                <button class="btn btn-sm btn-danger remove-favorite-btn" 
                                        data-meditation-id="{{ $meditation->id }}"
                                        title="Удалить из избранного">
                                    <i class="fas fa-heart"></i>
                                    <span class="ms-1">В избранном</span>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-danger add-favorite-btn" 
                                        data-meditation-id="{{ $meditation->id }}"
                                        title="Добавить в избранное">
                                    <i class="far fa-heart"></i>
                                    <span class="ms-1">В избранное</span>
                                </button>
                            @endif
                        </div>
                        @endauth
                    </div>
                </div>
                <div class="card-footer">
                    @if($meditation->is_premium && !auth()->user()?->canAccessPremiumContent())
                        <button class="btn btn-sm btn-warning w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#premiumModal"
                                data-meditation-id="{{ $meditation->id }}"
                                data-meditation-title="{{ $meditation->title }}">
                            <i class="fas fa-crown"></i> Требуется подписка
                        </button>
                    @else
                        <a href="{{ route('meditations.show', $meditation) }}" 
                        class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-play"></i> Слушать
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

<div class="modal fade" id="premiumModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Требуется подписка</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-crown fa-4x text-warning mb-3"></i>
                <h4>Медитация "<span id="premiumMeditationTitle"></span>" доступна по подписке</h4>
                <p class="text-muted">Оформите подписку для доступа ко всем премиум медитациям</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Позже</button>
                <a href="{{ route('subscriptions.plans') }}" class="btn btn-primary">
                    Выбрать тариф
                </a>
            </div>
        </div>
    </div>
</div>
    <div class="mt-4">
        {{ $meditations->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
    .meditation-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .meditation-card .card-body {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }
    
    .meditation-card .text-muted {
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        line-height: 1.4;
        max-height: 4.2em;
    }
    
    .meditation-card .card-footer {
        flex-shrink: 0;
        margin-top: auto;
    }
    
</style>
@endsection

@section('scripts')

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


document.addEventListener('DOMContentLoaded', function() {
    const premiumModal = document.getElementById('premiumModal');
    
    if (premiumModal) {
        premiumModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-meditation-title');
            
            document.getElementById('premiumMeditationTitle').textContent = title;
        });
    }

    const categorySelect = document.getElementById('category');
    const typeSelect = document.getElementById('type');
    const sortSelect = document.getElementById('sort');
    
    function applyFilters() {
        console.log('Фильтры изменены:', {
            category: categorySelect.value,
            type: typeSelect.value,
            sort: sortSelect.value
        });
    }
    
    categorySelect?.addEventListener('change', applyFilters);
    typeSelect?.addEventListener('change', applyFilters);
    sortSelect?.addEventListener('change', applyFilters);
});


document.addEventListener('DOMContentLoaded', function() {
    const premiumModal = document.getElementById('premiumModal');
    
    if (premiumModal) {
        premiumModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-meditation-title');
            
            document.getElementById('premiumMeditationTitle').textContent = title;
        });
    }
});


document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const typeSelect = document.getElementById('type');
    const sortSelect = document.getElementById('sort');
    
    function applyFilters() {
        console.log('Фильтры изменены:', {
            category: categorySelect.value,
            type: typeSelect.value,
            sort: sortSelect.value
        });
    }
    
    categorySelect.addEventListener('change', applyFilters);
    typeSelect.addEventListener('change', applyFilters);
    sortSelect.addEventListener('change', applyFilters);
});
</script>
@endsection