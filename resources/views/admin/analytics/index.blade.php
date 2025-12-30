@extends('layouts.admin')

@section('title', 'Аналитика')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Аналитика приложения</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Всего медитаций</h5>
                    <h2 class="display-4">{{ \App\Models\Meditation::count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Всего пользователей</h5>
                    <h2 class="display-4">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Активные подписки</h5>
                    <h2 class="display-4">{{ $activeSubscriptions }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Прослушиваний</h5>
                    <h2 class="display-4">{{ \App\Models\Meditation::sum('play_count') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Самые популярные медитации</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Название</th>
                                    <th>Категория</th>
                                    <th>Прослушиваний</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topMeditations as $index => $meditation)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $meditation->title }}</td>
                                    <td>{{ $meditation->category->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $meditation->play_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Медитации по категориям</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Статистика по категориям</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Категория</th>
                            <th>Кол-во медитаций</th>
                            <th>Премиум</th>
                            <th>Бесплатные</th>
                            <th>Доля</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meditationsByCategory as $category)
                        @php
                            $total = $category->meditations_count;
                            $premium = $category->meditations()->where('is_premium', true)->count();
                            $free = $total - $premium;
                            $percentage = $total > 0 ? round(($total / \App\Models\Meditation::count()) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $total }}</td>
                            <td>{{ $premium }}</td>
                            <td>{{ $free }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                        {{ $percentage }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryCanvas = document.getElementById('categoryChart');
    
    if (categoryCanvas) {
        const categoryCtx = categoryCanvas.getContext('2d');
        const categoryLabels = {!! json_encode($categoryStats->keys()) !!};
        const categoryData = {!! json_encode($categoryStats->values()) !!};
        
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#8AC926', '#1982C4'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    } else {
        console.log('Элемент categoryChart не найден');
    }
});
</script>
@endsection