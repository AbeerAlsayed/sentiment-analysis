<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    public function run()
    {
        $topics = [
            [
                'name' => 'Toyota',
                'image' => 'Toyota_logo.png'
            ],
            [
                'name' => 'Mercedes-Benz',
                'image' => 'Mercedes-Logo.png'
            ],
            [
                'name' => 'BMW',
                'image' => 'BMW.png'
            ],
        ];

        // Loop through topics and create them
        foreach ($topics as $topic) {
            // Save the topic with the image URL pointing to the public directory
            Topic::create([
                'name' => $topic['name'],
                // Generate the URL for the image stored in public/images
                'image' => asset('storage/images/' . $topic['image'])
            ]);
        }
    }
}
