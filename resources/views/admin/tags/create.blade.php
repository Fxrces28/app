@extends('layouts.admin')

@section('title', 'Создать тег')

@section('content')
<div class="container">
    <h1 class="mb-4">Создать тег</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="form-label">Название тега *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" 
                           placeholder="Например: релакс, сон, медитация, осознанность" required>
                    <div class="form-text">
                        <i class="fas fa-lightbulb"></i> Придумайте короткое и понятное название
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
            
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Создать тег
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const previewTag = document.getElementById('preview-tag');
    const previewSlug = document.getElementById('preview-slug');
    
    nameInput.addEventListener('input', function() {
        const name = this.value.trim();
        
        if (name) {
            previewTag.textContent = name;
            previewSlug.textContent = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-');
        } else {
            previewTag.textContent = 'Название тега';
            previewSlug.textContent = 'nazvanie-tega';
        }
    });
    
    document.querySelectorAll('.badge.bg-light').forEach(badge => {
        badge.addEventListener('click', function() {
            nameInput.value = this.textContent.trim();
            nameInput.dispatchEvent(new Event('input'));
        });
    });
});
</script>
@endsection

@section('styles')
<style>
.badge.bg-light {
    cursor: pointer;
    transition: all 0.2s;
}
.badge.bg-light:hover {
    background-color: #e9ecef !important;
    transform: translateY(-1px);
}
</style>
@endsection