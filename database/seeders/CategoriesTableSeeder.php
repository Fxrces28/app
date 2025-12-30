<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Для сна', 'slug' => 'dlya-sna', 'type' => 'sleep'],
            ['name' => 'Утренние', 'slug' => 'utrennie', 'type' => 'morning'],
            ['name' => 'На сосредоточение', 'slug' => 'na-so-sredotochenie', 'type' => 'focus'],
            ['name' => 'На снятие стресса', 'slug' => 'na-snyatie-stressa', 'type' => 'stress_relief'],
            ['name' => 'Для начинающих', 'slug' => 'dlya-nachinayuschih', 'type' => 'beginner'],
            ['name' => 'Продвинутые', 'slug' => 'prodvinutye', 'type' => 'advanced'],
            ['name' => 'На управление эмоциями', 'slug' => 'na-upravlenie-emotsiyami', 'type' => 'emotions'],
            ['name' => 'Тело и дыхание', 'slug' => 'telo-i-dyhanie', 'type' => 'body'],
            ['name' => 'Работа и продуктивность', 'slug' => 'rabota-i-produktivnost', 'type' => 'work'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'type' => $category['type']
                ]
            );
        }
    }
}