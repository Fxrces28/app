@extends('layouts.app')

@section('title', 'О мобильном приложении')

@section('content')
<div class="container">
    <div class="hero-section text-center py-5 mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
        <div class="container py-5">
            <h1 class="display-4 text-white fw-bold mb-4">
                <i class="fas fa-moon"></i> Твое состояние
            </h1>
            <p class="lead text-white mb-4" style="font-size: 1.5rem;">
                Ваш персональный помощник для качественного сна и ментального здоровья
            </p>
            <div class="mt-4">
                <span class="badge bg-light text-dark me-2 p-2">
                    <i class="fas fa-star"></i> 4.8/5 в App Store
                </span>
                <span class="badge bg-light text-dark me-2 p-2">
                    <i class="fas fa-download"></i> 50K+ скачиваний
                </span>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-heart"></i> 95% рекомендуют
                </span>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h2 class="mb-4">Почему мы?</h2>
            <p class="lead mb-4">
                Наше приложение создано на основе современных научных исследований о влиянии режима сна 
                и практик осознанности на ментальное здоровье. Мы объединили технологии и психологию, 
                чтобы помочь вам улучшить качество жизни.
            </p>
        </div>
    </div>
    <!--
    <h2 class="text-center mb-5">Ключевые функции</h2>
    <div class="row g-4 mb-5">
        @foreach($features as $feature)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3" style="font-size: 3rem; color: #667eea;">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    <h4 class="card-title mb-3">{{ $feature['title'] }}</h4>
                    <p class="card-text text-muted">{{ $feature['description'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>-->

    <h2 class="text-center mb-4">Что говорят пользователи</h2>
    <div class="row g-4 mb-5">
        @foreach($testimonials as $testimonial)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-3">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-warning"></i>
                        @endfor
                    </div>
                    <p class="card-text fst-italic mb-4">"{{ $testimonial['text'] }}"</p>
                    <p class="card-text text-end text-muted mb-0"><strong>{{ $testimonial['author'] }}</strong></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <h2 class="mb-4">Для кого создано приложение?</h2>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Для людей с нарушениями сна и бессонницей
                    </li>
                    <li class="list-group-item border-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Для тех, кто испытывает стресс и тревожность
                    </li>
                    <li class="list-group-item border-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Для желающих улучшить качество сна
                    </li>
                    <li class="list-group-item border-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Для специалистов-психологов
                    </li>
                    <li class="list-group-item border-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Для всех, кто заботится о ментальном здоровье
                    </li>
                </ul>
            </div>
        </div>

    <div class="row mb-5">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="fas fa-mobile-alt"></i> Технические требования</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> iOS 12.0 и выше</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Android 8.0 и выше</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 100 МБ свободного места</li>
                        <li><i class="fas fa-check text-success me-2"></i> Интернет-соединение для синхронизации</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-download"></i> Скачать приложение</h4>
                </div>
                <div class="card-body text-center">
                    <p class="mb-4">Доступно в официальных магазинах приложений</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-dark btn-lg">
                             App Store
                        </a>
                        <a href="#" class="btn btn-dark btn-lg">
                            <i class="fab fa-google-play"></i> Google Play
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center py-4" style="background-color: #f8f9fa; border-radius: 10px;">
        <h3 class="mb-3">Вопросы или предложения?</h3>
        <p class="mb-4">Свяжитесь с нашей командой поддержки</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="mailto:support@sleepwell.com" class="btn btn-outline-primary">
                <i class="fas fa-envelope"></i> support@sleepwell.com
            </a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.hover-shadow {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.feature-icon {
    transition: transform 0.3s ease;
}
.card:hover .feature-icon {
    transform: scale(1.1);
}
</style>
@endsection