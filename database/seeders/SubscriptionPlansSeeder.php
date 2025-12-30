<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'Бесплатный',
                'slug' => 'free',
                'description' => 'Базовый доступ к медитациям',
                'price' => 0,
                'duration' => 'lifetime',
                'duration_days' => 9999,
                'features' => json_encode(['Доступ к 5 бесплатным медитациям', 'Базовая статистика']),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'Месячная подписка',
                'slug' => 'monthly',
                'description' => 'Полный доступ на 1 месяц',
                'price' => 299,
                'duration' => 'month',
                'duration_days' => 30,
                'features' => json_encode(['Доступ ко всем медитациям', 'Без рекламы', 'Персональные рекомендации', 'Расширенная статистика']),
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Годовая подписка',
                'slug' => 'yearly',
                'description' => 'Экономия 20% при оплате за год',
                'price' => 2870,
                'duration' => 'year',
                'duration_days' => 365,
                'features' => json_encode(['Доступ ко всем медитациям', 'Без рекламы', 'Персональные рекомендации', 'Расширенная статистика', 'Приоритетная поддержка']),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3
            ],
            [
                'name' => 'Пожизненный доступ',
                'slug' => 'lifetime',
                'description' => 'Пожизненный доступ ко всем функциям',
                'price' => 9990,
                'duration' => 'lifetime',
                'duration_days' => 99999,
                'features' => json_encode(['Доступ ко всем медитациям навсегда', 'Без рекламы', 'Все будущие обновления', 'Эксклюзивный контент', 'Приоритетная поддержка']),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}