@extends('layouts.admin')

@section('title', 'Редактировать категорию')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать категорию: {{ $category->name }}</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Название *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="type" class="form-label">Тип категории *</label>
                    <select class="form-select @error('type') is-invalid @enderror" 
                            id="type" name="type" required>
                        <option value="">Выберите тип</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" 
                                {{ old('type', $category->type) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                            
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить
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
function confirmDelete(id, name) {
    if (confirm(`Вы уверены, что хотите удалить категорию "${name}"?\n\nЭто может повлиять на медитации, связанные с этой категорией.`)) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/categories/' + id;
        form.submit();
    }
}
</script>
@endsection