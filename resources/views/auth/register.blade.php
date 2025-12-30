@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus"></i> Создать аккаунт</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Имя -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя</label>
                            <input id="name" type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" 
                                   required autocomplete="name" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Пароль -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Минимум 8 символов</small>
                        </div>

                        <!-- Подтверждение пароля -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                            <input id="password_confirmation" type="password" 
                                   class="form-control" 
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <!-- Кнопка регистрации -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i> Зарегистрироваться
                            </button>
                        </div>

                        <!-- Ссылка на вход -->
                        <div class="mt-4 text-center">
                            <p class="mb-0">Уже есть аккаунт? 
                                <a href="{{ route('login') }}" class="text-success fw-bold">
                                    Войдите
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection