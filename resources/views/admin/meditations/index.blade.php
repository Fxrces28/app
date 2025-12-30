@extends('layouts.admin')

@section('title', 'Управление медитациями')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Управление медитациями</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Список медитаций</span>
            <a href="{{ route('admin.meditations.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Добавить медитацию
            </a>
        </div>
        
        <div class="card-body">
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Длительность</th>
                            <th>Премиум</th>
                            <th>Прослушиваний</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meditations as $meditation)
                        <tr>
                            <td>{{ $meditation->id }}</td>
                            <td>{{ $meditation->title }}</td>
                            <td>{{ $meditation->category->name ?? '-' }}</td>
                            <td>{{ gmdate('i:s', $meditation->duration) }}</td>
                            <td>
                                @if($meditation->is_premium)
                                    <span class="badge bg-warning">Премиум</span>
                                @else
                                    <span class="badge bg-success">Бесплатно</span>
                                @endif
                            </td>
                            <td>{{ $meditation->play_count }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.meditations.edit', $meditation) }}" 
                                    class="btn btn-primary" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.meditations.destroy', $meditation) }}" 
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-meditation-btn" 
                                                data-meditation-title="{{ $meditation->title }}"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Медитаций пока нет</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $meditations->links() }}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-meditation-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const meditationTitle = this.getAttribute('data-meditation-title');
            const form = this.closest('form');
            
            if (confirm(`Удалить медитацию "${meditationTitle}"?`)) {
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
                            showAlert('success', data.message || 'Медитация удалена успешно');
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