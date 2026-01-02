@extends('layouts.app')

@section('title', 'Мои избранные медитации')

@section('content')
<div class="container">
    <h1 class="mb-4">Мои избранные медитации</h1>
    
    @if($favorites->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <h4>У вас пока нет избранных медитаций</h4>
            <p>Добавляйте медитации в избранное, чтобы легко находить их позже</p>
            <a href="{{ route('meditations.index') }}" class="btn btn-primary">
                Найти медитации
            </a>
        </div>
    </div>
    @else
    <div class="row g-4">
        @foreach($favorites as $meditation)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100">
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
                </div>
                <div class="card-footer">
                    <a href="{{ route('meditations.show', $meditation) }}" 
                       class="btn btn-sm btn-primary w-100 mb-2">
                        <i class="fas fa-play"></i> Слушать
                    </a>
                    
                    <button class="btn btn-sm btn-danger w-100 remove-favorite-btn" 
                            data-meditation-id="{{ $meditation->id }}"
                            data-is-favorite="true">
                        <i class="fas fa-heart"></i> Удалить из избранного
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $favorites->links() }}
    </div>
    @endif
</div>
<style>
    .col-md-4 {
        transition: all 0.3s ease;
    }
    
    .add-favorite-btn, .remove-favorite-btn {
        transition: all 0.2s ease;
    }
</style>
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
@endsection