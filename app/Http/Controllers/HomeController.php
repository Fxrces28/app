<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index', [
            'title' => 'Главная страница'
        ]);
    }
    public function adminDashboard()
{
    $stats = [
        'meditations' => \App\Models\Meditation::count(),
        'categories' => \App\Models\Category::count(),
        'users' => \App\Models\User::count(),
    ];
    
    try {
        $activeSubscriptions = \App\Models\Payment::where('status', 'succeeded')
            ->where('subscription_ends_at', '>', now())
            ->distinct('user_id')
            ->count('user_id');
            
        if ($activeSubscriptions === 0 && \Schema::hasTable('subscriptions')) {
            $activeSubscriptions = \App\Models\Subscription::where('is_active', true)
                ->where('ends_at', '>', now())
                ->distinct('user_id')
                ->count('user_id');
        }
        
        $stats['active_subscriptions'] = $activeSubscriptions;
        
    } catch (\Exception $e) {
        $stats['active_subscriptions'] = 0;
    }

    return view('admin.dashboard', compact('stats'));
}

    public function about()
    {
        $features = [
            [
                'icon' => 'fas fa-moon',
                'title' => 'Интеллектуальный планировщик сна',
                'description' => 'Автоматический расчет оптимального времени отхода ко сну на основе ваших предпочтений и расписания'
            ],
            [
                'icon' => 'fas fa-bell',
                'title' => 'Адаптивные уведомления',
                'description' => 'Умные напоминания и будильники, которые подстраиваются под ваш график'
            ],
            [
                'icon' => 'fas fa-headphones',
                'title' => 'Библиотека терапевтического аудиоконтента',
                'description' => 'Коллекция медитаций для сна, пробуждения и управления эмоциональным состоянием'
            ],
            [
                'icon' => 'fas fa-user-md',
                'title' => 'Удаленное управление для специалиста',
                'description' => 'Возможность для психолога отслеживать прогресс и настраивать программу'
            ],
            [
                'icon' => 'fas fa-chart-line',
                'title' => 'Персонализированные рекомендации',
                'description' => 'Алгоритмы анализируют ваши привычки и предлагают индивидуальные решения'
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'title' => 'Конфиденциальность и безопасность',
                'description' => 'Все данные защищены и хранятся в соответствии с законодательством'
            ]
        ];

        $testimonials = [
            [
                'text' => 'Это приложение изменило мой подход ко сну. Теперь я высыпаюсь и чувствую себя гораздо лучше!',
                'author' => 'Анна, 28 лет'
            ],
            [
                'text' => 'Как психолог, рекомендую это приложение своим клиентам. Отличный инструмент для работы с тревожностью.',
                'author' => 'Дмитрий, 32 года'
            ],
            [
                'text' => 'Медитации перед сном стали моим ежедневным ритуалом. Качество сна улучшилось на 100%.',
                'author' => 'Сергей, 35 лет'
            ]
        ];

        return view('home.about', compact('features', 'testimonials'));
    }
}