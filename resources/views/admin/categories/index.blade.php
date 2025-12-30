@extends('layouts.admin')

@section('title', 'Управление категориями')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Управление категориями</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Добавить категорию
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Slug</th>
                            <th>Кол-во медитаций</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->meditations_count ?? $category->meditations()->count() }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="btn btn-primary" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-category-btn" 
                                                data-category-name="{{ $category->name }}"
                                                data-meditation-count="{{ $category->meditations_count ?? 0 }}"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Категорий пока нет</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-category-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const categoryName = this.getAttribute('data-category-name');
            const meditationCount = parseInt(this.getAttribute('data-meditation-count'));
            const form = this.closest('form');
            
            let message = `Удалить категорию "${categoryName}"?`;
            
            if (meditationCount > 0) {
                message += `\n\n⚠️ Внимание: в этой категории ${meditationCount} медитаций.`;
                message += `\nСначала удалите или переместите эти медитации.`;
            }
            
            if (confirm(message)) {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = this.closest('tr');
                        row.style.transition = 'all 0.3s';
                        row.style.opacity = '0';
                        row.style.height = '0';
                        row.style.padding = '0';
                        row.style.margin = '0';
                        
                        setTimeout(() => {
                            row.remove();
                            showAlert('success', data.message || 'Категория удалена успешно');
                        }, 300);
                    } else {
                        showAlert('danger', data.message || 'Ошибка при удалении');
                        this.innerHTML = originalHtml;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Ошибка сети');
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                });
            }
        });
    });
});

function showAlert(type, message) {
    document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `custom-alert alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    
    alert.innerHTML = `
        <strong>${type === 'success' ? 'Успешно!' : 'Ошибка!'}</strong> ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => alert.remove(), 5000);
}
</script>
@endsection