<?php

namespace App\Services;

use App\Enum\SentimentEnum;
use App\Models\Comment;
use App\Models\Topic;

class CommentService
{
    public function getCommentsByFilters(?string $topicName = null, ?SentimentEnum $sentiment = null)
    {
        $query = Comment::with('user');
        if ($topicName) {
            $topic = Topic::where('name', $topicName)->first();
            if ($topic) {
                $query->where('topic_id', $topic->id);
            }
        }
        if ($sentiment) {
            $query->where('sentiment', $sentiment->value);
        }
        return $query->get();
    }

    public function createComment(int $userId, int $topicId, string $content, string $sentiment): Comment
    {
        return Comment::create([
            'user_id' => $userId,
            'topic_id' => $topicId,
            'content' => $content,
            'sentiment' => $sentiment,
        ]);
    }

    public function getTopicsWithUserComments($user)
    {
        $topics = Topic::with(['comments' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        return $topics->filter(function ($topic) {
            return $topic->comments->isNotEmpty();
        });
    }
}
