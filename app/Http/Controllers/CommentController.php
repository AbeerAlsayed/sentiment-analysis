<?php

namespace App\Http\Controllers;

use App\Enum\SentimentEnum;
use App\Http\Requests\GetCommentsByTopicRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Topic;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected $commentService;
    protected $sentimentService;

    // حقن الـ Service في الـ Controller
    public function __construct(CommentService $commentService,SentimentAnalysisService $sentimentService)
    {
        $this->commentService = $commentService;
        $this->sentimentService = $sentimentService;

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'topic_id' => 'required|exists:topics,id',
            'content' => 'required|string',
        ]);
        // تحليل المشاعر
        $sentiment = $this->sentimentService->analyze($validated['content']);

        // تخزين التعليق في قاعدة البيانات
        $comment = Comment::create([
            'user_id' => $validated['user_id'],
            'topic_id' => $validated['topic_id'],
            'content' => $validated['content'],
            'sentiment' => $sentiment,
        ]);

        return response()->json(new CommentResource($comment), 201);
    }

    public function getCommentsByTopic(GetCommentsByTopicRequest $request)
    {
        // الحصول على `topic_name` و `sentiment` إذا كانا موجودين في الطلب
        $topicName = $request->input('topic_name');  // اختياري
        $sentiment = $request->input('sentiment');    // اختياري

        // تحويل `sentiment` إلى Enum إذا تم تمريره
        if ($sentiment) {
            $sentiment = SentimentEnum::from($sentiment);  // تحويل النص إلى Enum
        }
        // جلب التعليقات باستخدام CommentService مع تطبيق الفلاتر
        $comments = $this->commentService->getCommentsByFilters($topicName, $sentiment);

        // إرجاع التعليقات باستخدام CommentResource
        return CommentResource::collection($comments);
    }
}
