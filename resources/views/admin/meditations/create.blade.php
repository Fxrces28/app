@extends('layouts.admin')

@section('title', 'Добавить медитацию')

@section('content')
<div class="container">
    <h1 class="mb-4">Добавить новую медитацию</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.meditations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
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
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                   id="duration" name="duration" value="{{ old('duration') }}" 
                                   min="60" max="3600" required>
                            <small class="text-muted">От 60 секунд (1 минута) до 3600 (1 час)</small>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tags" class="form-label">Теги</label>
                            <select class="form-select" id="tags" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
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
                                       {{ old('is_premium') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_premium">
                                    Премиум контент
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="audio_file" class="form-label">Аудиофайл *</label>
                            <input type="file" class="form-control" id="audio_file" name="audio_file" 
                                accept=".mp3,.wav,.m4a" required onchange="previewAudio(this)">
                            <small class="text-muted">MP3, WAV, M4A до 10MB</small>
                            <div id="audio-preview" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Изображение (обложка)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">JPG, PNG до 2MB</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.meditations.index') }}" class="btn btn-secondary">
                        Назад к списку
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить медитацию
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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
</script>