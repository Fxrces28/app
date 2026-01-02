@extends('layouts.admin')

@section('title', 'Редактировать тарифный план')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать тарифный план: {{ $subscription->name }}</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $subscription->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $subscription->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Цена (₽) *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $subscription->price) }}" required>
                                <span class="input-group-text">₽</span>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Техническое имя</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $subscription->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Только латинские буквы, цифры и дефисы. Оставьте пустым для автоматической генерации</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Длительность *</label>
                            <select class="form-select @error('duration') is-invalid @enderror" 
                                    id="duration" name="duration" required>
                                <option value="">Выберите длительность</option>
                                <option value="month" {{ old('duration', $subscription->duration) == 'month' ? 'selected' : '' }}>Месяц</option>
                                <option value="6months" {{ old('duration', $subscription->duration) == '6months' ? 'selected' : '' }}>6 месяцев</option>
                                <option value="year" {{ old('duration', $subscription->duration) == 'year' ? 'selected' : '' }}>Год</option>
                            </select>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
  
                        </div>
                        
                        <div class="mb-3">
                            <label for="features" class="form-label">Возможности (каждая с новой строки)</label>
                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                      id="features" name="features" rows="4" 
                                      placeholder="Доступ ко всем медитациям
Без рекламы
Персональные рекомендации">{{ old('features', $subscription->features ? implode("\n", json_decode($subscription->features, true)) : '') }}</textarea>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Порядок сортировки</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $subscription->sort_order) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Активный тариф</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_popular" name="is_popular" value="1"
                                   {{ old('is_popular', $subscription->is_popular) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_popular">
                                <strong>Популярный тариф</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Создан: {{ $subscription->created_at->format('d.m.Y H:i') }}<br>
                                    Обновлен: {{ $subscription->updated_at->format('d.m.Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmDelete({{ $subscription->id }}, '{{ addslashes($subscription->name) }}')">
                            <i class="fas fa-trash"></i> Удалить
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
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
function confirmDelete(id, name) {
    if (confirm(`Вы уверены, что хотите удалить тарифный план "${name}"?\n\nЭто действие удалит все связанные активные подписки пользователей!`)) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/subscriptions/' + id;
        form.submit();
    }
}
</script>
@endsection

@section('styles')
<style>
.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}
.card.border-primary {
    border-width: 2px;
}
#preview-features ul li {
    margin-bottom: 0.5rem;
}
</style>
@endsection