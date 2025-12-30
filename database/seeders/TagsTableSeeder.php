<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            ['name' => 'релакс', 'slug' => 'relax'],
            ['name' => 'сон', 'slug' => 'sleep'],
            ['name' => 'медитация', 'slug' => 'meditation'],
            ['name' => 'осознанность', 'slug' => 'mindfulness'],
            ['name' => 'тревога', 'slug' => 'anxiety'],
            ['name' => 'стресс', 'slug' => 'stress'],
            ['name' => 'энергия', 'slug' => 'energy'],
            ['name' => 'фокус', 'slug' => 'focus'],
            ['name' => 'дыхание', 'slug' => 'breathing'],
            ['name' => 'визуализация', 'slug' => 'visualization'],
            ['name' => 'исцеление', 'slug' => 'healing'],
            ['name' => 'дети', 'slug' => 'kids'],
            ['name' => 'йога-нидра', 'slug' => 'yoga-nidra'],
            ['name' => 'гнев', 'slug' => 'anger'],
            ['name' => 'мотивация', 'slug' => 'motivation'],
            ['name' => 'креативность', 'slug' => 'creativity'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag['slug']],
                ['name' => $tag['name']]
            );
        }
    }
}