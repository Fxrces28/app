@extends('layouts.admin')

@section('title', 'Админ-панель')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Админ-панель</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Медитации</h5>
                            <h2 class="display-4">{{ $stats['meditations'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-music fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.meditations.index') }}" class="text-white text-decoration-none">
                        <small>Управление <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Пользователи</h5>
                            <h2 class="display-4">{{ $stats['users'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Подписки</h5>
                            <h2 class="display-4">{{ $stats['active_subscriptions'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-crown fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.subscriptions.index') }}" class="text-dark text-decoration-none">
                        <small>Управление <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.meditations.create') }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus"></i> Добавить медитацию
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-list"></i> Добавить категорию
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.tags.create') }}" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-tag"></i> Добавить тег
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-crown"></i> Добавить тариф
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"></i> Последние медитации</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $latestMeditations = \App\Models\Meditation::latest()->limit(5)->get();
                                @endphp
                                @foreach($latestMeditations as $meditation)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.meditations.edit', $meditation) }}">
                                            {{ Str::limit($meditation->title, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $meditation->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        @if($meditation->is_premium)
                                            <span class="badge bg-warning">Премиум</span>
                                        @else
                                            <span class="badge bg-success">Бесплатно</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"></i> Популярные медитации</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Прослушиваний</th>
                                    <th>Категория</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $popularMeditations = \App\Models\Meditation::orderBy('play_count', 'desc')->limit(5)->get();
                                @endphp
                                @foreach($popularMeditations as $meditation)
                                <tr>
                                    <td>{{ Str::limit($meditation->title, 30) }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $meditation->play_count }}</span>
                                    </td>
                                    <td>{{ $meditation->category->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection