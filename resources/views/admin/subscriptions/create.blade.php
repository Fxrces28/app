@extends('layouts.admin')

@section('title', 'Создать тарифный план')

@section('content')
<div class="container">
    <h1 class="mb-4">Создать новый тарифный план</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Цена (₽) *</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', 0) }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Длительность *</label>
                            <select class="form-select @error('duration') is-invalid @enderror" 
                                    id="duration" name="duration" required>
                                <option value="">Выберите длительность</option>
                                <option value="month" {{ old('duration') == 'month' ? 'selected' : '' }}>Месяц</option>
                                <option value="6months" {{ old('duration') == '6months' ? 'selected' : '' }}>6 месяцев</option>
                                <option value="year" {{ old('duration') == 'year' ? 'selected' : '' }}>Год</option>
                            </select>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="features" class="form-label">Возможности (каждая с новой строки)</label>
                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                      id="features" name="features" rows="4" placeholder="Доступ ко всем медитациям
Без рекламы
Персональные рекомендации">{{ old('features') }}</textarea>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Порядок сортировки</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Активный тариф
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_popular" name="is_popular" value="1">
                                <label class="form-check-label" for="is_popular">
                                    Популярный тариф
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить тариф
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection