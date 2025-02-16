<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Topic;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $positiveComments = [
            'Amazing car! I love the design and performance.',
            'Very smooth drive, totally worth the price!',
            'Incredible fuel efficiency, highly recommended!',
            'Best car I have ever owned, super comfortable.',
            'Luxury and speed combined perfectly!'
        ];

        $negativeComments = [
            'Terrible experience, not worth the money.',
            'Very uncomfortable seats and poor handling.',
            'Had so many issues with the engine, disappointed!',
            'Too expensive for the features it offers.',
            'Not reliable at all, would not recommend it.'
        ];

        $topics = Topic::all(); // جلب جميع المواضيع (شركات السيارات)
        $users = User::all();   // جلب جميع المستخدمين

        if ($topics->isEmpty() || $users->isEmpty()) {
            return; // التأكد من وجود بيانات في الجداول قبل الإدخال
        }

        foreach ($topics as $topic) {
            for ($i = 0; $i < 5; $i++) { // إنشاء 5 تعليقات لكل موضوع
                $isPositive = rand(0, 1); // تحديد إذا كان التعليق إيجابي أو سلبي
                $text = $isPositive ? $positiveComments[array_rand($positiveComments)] : $negativeComments[array_rand($negativeComments)];
                $sentiment = $isPositive ? 'positive' : 'negative';

                Comment::create([
                    'content' => $text,
                    'user_id' => $users->random()->id, // اختيار مستخدم عشوائي
                    'topic_id' => $topic->id,
                    'sentiment' => $sentiment,
                ]);
            }
        }
    }
}
