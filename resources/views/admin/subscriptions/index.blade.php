@extends('layouts.admin')

@section('title', 'Управление подписками')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Управление тарифными планами</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Тарифные планы</span>
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Добавить тариф
            </a>
        </div>
        
        <div class="card-body">
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Длительность</th>
                            <th>Статус</th>
                            <th>Популярный</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>
                            <td>
                                <strong>{{ $plan->name }}</strong>
                                @if($plan->description)
                                    <br><small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($plan->price == 0)
                                    Бесплатно
                                @else
                                    {{ number_format($plan->price, 2) }} ₽
                                @endif
                            </td>
                            <td>
                                @switch($plan->duration)
                                    @case('month')
                                        <span class="badge bg-info">Месяц</span>
                                        @break
                                    @case('6months')
                                        <span class="badge bg-primary">6 месяцев</span>
                                        @break
                                    @case('year')
                                        <span class="badge bg-warning">Год</span>
                                        @break
                                @endswitch
                                <br>
                                <small>{{ $plan->duration_days }} дней</small>
                            </td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Активен</span>
                                @else
                                    <span class="badge bg-danger">Неактивен</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->is_popular)
                                    <span class="badge bg-warning">Популярный</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.subscriptions.edit', $plan) }}" 
                                       class="btn btn-primary" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.subscriptions.destroy', $plan) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-plan-btn" 
                                                data-plan-name="{{ $plan->name }}"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Тарифных планов пока нет</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-plan-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const planName = this.getAttribute('data-plan-name');
            const form = this.closest('form');
            
            if (confirm(`Удалить тарифный план "${planName}"?\n\nЭто действие удалит все связанные активные подписки пользователей!`)) {
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
                            showAlert('success', data.message || 'Тарифный план удален успешно');
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