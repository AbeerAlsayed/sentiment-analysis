<?php

namespace App\Http\Controllers;

use App\Enum\SentimentEnum;
use App\Http\Requests\GetCommentsByTopicRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected $commentService;

    // حقن الـ Service في الـ Controller
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
