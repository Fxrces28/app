@extends('layouts.admin')

@section('title', 'Редактировать медитацию')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать медитацию: {{ $meditation->title }}</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.meditations.update', $meditation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $meditation->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Категория *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Выберите категорию</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $meditation->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">Длительность (секунды) *</label>
                            <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                   id="duration" name="duration" value="{{ old('duration', $meditation->duration) }}" 
                                   min="60" max="3600" required>
                            <small class="text-muted">От 60 секунд (1 минута) до 3600 (1 час)</small>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tags" class="form-label">Теги</label>
                            <select class="form-select" id="tags" name="tags[]" multiple size="4">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" 
                                            {{ in_array($tag->id, $selectedTags) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Удерживайте Ctrl для выбора нескольких тегов</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="editor" name="description" rows="8">{{ old('description', $meditation->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_premium" name="is_premium" value="1"
                                       {{ old('is_premium', $meditation->is_premium) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_premium">
                                    Премиум контент
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Текущий аудиофайл</label>
                            <div class="alert alert-info">
                                <i class="fas fa-music"></i> 
                                {{ basename($meditation->audio_path) }}
                                <br>
                                <small>Длительность: {{ gmdate('i:s', $meditation->duration) }}</small>
                                <br>
                                <audio controls class="mt-2" style="width: 100%;">
                                    <source src="{{ asset('storage/' . $meditation->audio_path) }}" type="audio/mpeg">
                                    Ваш браузер не поддерживает аудио.
                                </audio>
                            </div>
                        </div>

                        @if($meditation->image_path)
                        <div class="mb-3">
                            <label class="form-label">Текущее изображение</label>
                            <div>
                                <img src="{{ asset('storage/' . $meditation->image_path) }}" 
                                    alt="Обложка" class="img-thumbnail" style="max-height: 150px;">
                                <p class="mt-1">
                                    <small>{{ basename($meditation->image_path) }}</small>
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="mb-3">
                            <label class="form-label">Текущее изображение</label>
                            <div class="alert alert-warning">
                                <i class="fas fa-image"></i> Изображение не загружено
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="audio_file" class="form-label">Заменить аудиофайл</label>
                            <input type="file" class="form-control @error('audio_file') is-invalid @enderror" 
                                   id="audio_file" name="audio_file" accept=".mp3,.wav,.m4a">
                            <small class="text-muted">Оставьте пустым, чтобы не менять файл. MP3, WAV, M4A до 10MB</small>
                            @error('audio_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Заменить изображение</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Оставьте пустым, чтобы не менять изображение. JPG, PNG до 2MB</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.meditations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        <button type="button" class="btn btn-danger delete-btn" 
                            data-id="{{ $meditation->id }}"
                            data-title="{{ $meditation->title }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading', '|', 
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
            ],
            language: 'ru',
            heading: {
                options: [
                    { model: 'paragraph', title: 'Параграф', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Заголовок 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Заголовок 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Заголовок 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
});


function previewAudio(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const audio = document.createElement('audio');
        audio.controls = true;
        audio.style.width = '100%';
        
        const source = document.createElement('source');
        source.src = URL.createObjectURL(file);
        source.type = file.type;
        
        audio.appendChild(source);
        
        const preview = document.getElementById('audio-preview');
        preview.innerHTML = '';
        preview.appendChild(audio);
    }
}

function confirmDelete(id, title) {
    if (confirm('Вы уверены, что хотите удалить медитацию "' + title + '"?\nЭто действие нельзя отменить!')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/meditations/' + id;
        form.submit();
    }
}

document.getElementById('audio_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        alert('Будет загружен новый файл: ' + file.name);
    }
});

document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.className = 'img-thumbnail mt-2';
            preview.style.maxHeight = '150px';
            
            const container = document.getElementById('image').parentNode;
            const oldPreview = container.querySelector('.image-preview');
            if (oldPreview) oldPreview.remove();
            
            preview.className += ' image-preview';
            container.appendChild(preview);
        }
        reader.readAsDataURL(file);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const meditationId = this.getAttribute('data-id');
            const meditationTitle = this.getAttribute('data-title');
            
            if (confirm(`Удалить медитацию "${meditationTitle}"?`)) {
                const row = this.closest('tr');
                row.innerHTML = '<td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm"></div> Удаление...</td>';
                
                fetch(`/admin/meditations/${meditationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.style.transition = 'all 0.3s';
                        row.style.opacity = '0';
                        row.style.height = '0';
                        row.style.padding = '0';
                        row.style.margin = '0';
                        
                        setTimeout(() => {
                            row.remove();
                            showAlert('success', data.message || 'Медитация удалена');
                        }, 300);
                    } else {
                        showAlert('danger', data.message || 'Ошибка при удалении');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Ошибка сети');
                    location.reload();
                });
            }
        });
    });
});

function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
@endsection