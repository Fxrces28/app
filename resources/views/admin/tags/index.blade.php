@extends('layouts.admin')

@section('title', 'Управление тегами')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Управление тегами</h1>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Добавить тег
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
                            <th>Медитаций</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td><code>{{ $tag->slug }}</code></td>
                            <td>{{ $tag->meditations_count ?? $tag->meditations()->count() }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.tags.edit', $tag) }}" 
                                       class="btn btn-primary" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tags.destroy', $tag) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-tag-btn" 
                                                data-tag-name="{{ $tag->name }}"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Тегов пока нет</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($tags->hasPages())
            <div class="mt-3">
                {{ $tags->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-tag-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tagName = this.getAttribute('data-tag-name');
            const form = this.closest('form');
            
            if (confirm(`Удалить тег "${tagName}"?`)) {
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
                            showAlert('success', data.message || 'Тег удален успешно');
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