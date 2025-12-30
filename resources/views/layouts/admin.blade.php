<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Твое состояние админ-панель - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CKEditor 5 CSS -->
    <style>.ck-editor__editable { min-height: 300px; }</style>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/ckeditor.js"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-moon"></i> Твое состояние
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('admin.meditations.index') }}">
                <i class="fas fa-music"></i> Медитации
            </a>
            <a class="nav-link" href="{{ route('admin.categories.index') }}">
                <i class="fas fa-list"></i> Категории
            </a>
            <a class="nav-link" href="{{ route('admin.tags.index') }}">
                <i class="fas fa-tags"></i> Теги
            </a>
            <a class="nav-link" href="{{ route('admin.subscriptions.index') }}">
                <i class="fas fa-crown"></i> Подписки
            </a>
            <a class="nav-link" href="{{ route('admin.analytics.index') }}">
                <i class="fas fa-chart-line"></i> Аналитика
            </a>
            <a class="nav-link" href="{{ route('home') }}">
                <i class="fas fa-external-link-alt"></i> Сайт для пользователей
            </a>
        </div>
    </div>
</nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</body>
</html>