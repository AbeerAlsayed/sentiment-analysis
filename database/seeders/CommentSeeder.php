<?php

namespace Database\Seeders;

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

        $topics = Topic::all(); // جلب جميع المواضيع
        $users = User::all();   // جلب جميع المستخدمين

        if ($topics->isEmpty() || $users->isEmpty()) {
            return; // التأكد من وجود بيانات قبل الإدراج
        }

        foreach ($topics as $topic) {
            // عدد عشوائي من التعليقات لكل موضوع (من 3 إلى 7)
            $commentCount = rand(3, 7);
            $selectedUsers = $users->random(min($commentCount, $users->count())); // اختيار مستخدمين عشوائيًا

            foreach ($selectedUsers as $user) {
                $isPositive = rand(0, 1); // تحديد إذا كان التعليق إيجابي أو سلبي
                $text = $isPositive ? $positiveComments[array_rand($positiveComments)] : $negativeComments[array_rand($negativeComments)];
                $sentiment = $isPositive ? 'positive' : 'negative';

                Comment::create([
                    'content' => $text,
                    'user_id' => $user->id, // مستخدم عشوائي لكل تعليق
                    'topic_id' => $topic->id, // نفس الموضوع لكل مجموعة تعليقات
                    'sentiment' => $sentiment,
                ]);
            }
        }
    }
}
