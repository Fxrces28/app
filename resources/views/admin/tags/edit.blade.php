@extends('layouts.admin')

@section('title', 'Редактировать тег')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать тег: {{ $tag->name }}</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tags.update', $tag) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="name" class="form-label">Название тега *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $tag->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Назад
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить
                            </button>
                        </div>
                    </div>
                    
                    
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
    const nameInput = document.getElementById('name');
    
    document.querySelector('form').addEventListener('submit', function(e) {
        const newName = nameInput.value.trim();
        if (newName && newName !== '{{ $tag->name }}') {
            if (!confirm(`Изменить тег с "{{ $tag->name }}" на "${newName}"?\n\nЭто обновится во всех связанных медитациях.`)) {
                e.preventDefault();
            }
        }
    });
});

function confirmDelete(id, name) {
    const meditationCount = {{ $tag->meditations()->count() }};
    
    let message = `Вы уверены, что хотите удалить тег "${name}"?`;
    
    if (meditationCount > 0) {
        message += `\n\n⚠️ Внимание: этот тег используется в ${meditationCount} медитациях.`;
        message += `\nТег будет удален из всех медитаций.`;
    }
    
    if (confirm(message)) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/tags/' + id;
        form.submit();
    }
}
</script>
@endsection