<?php

namespace App\Services;

use App\Enum\SentimentEnum;
use App\Models\Comment;
use App\Models\Topic;

class CommentService
{
    public function getCommentsByFilters(?string $topicName = null, ?SentimentEnum $sentiment = null)
    {
        // إنشاء استعلام لجلب التعليقات
        $query = Comment::with('user');

        // إضافة فلتر الموضوع إذا تم تمريره
        if ($topicName) {
            $topic = Topic::where('name', $topicName)->first();
            if ($topic) {
                $query->where('topic_id', $topic->id);
            }
        }

        // إضافة فلتر التصنيف إذا تم تمريره
        if ($sentiment) {
            $query->where('sentiment', $sentiment->value);  // استخدام قيمة الـ Enum
        }

        // إرجاع التعليقات بعد تطبيق الفلاتر
        return $query->get();
    }
}
