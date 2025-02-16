<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    public function run()
    {
        $topics = [
            [
                'name' => 'Toyota',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/9/9d/Toyota_logo.png'
            ],
            [
                'name' => 'Mercedes-Benz',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/9/90/Mercedes-Logo.png'
            ],
            [
                'name' => 'BMW',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/4/44/BMW.svg'
            ],
        ];

        foreach ($topics as $topic) {
            Topic::create($topic);
        }
    }
}

