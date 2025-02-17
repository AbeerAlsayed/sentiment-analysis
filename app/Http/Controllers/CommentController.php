<?php

namespace App\Http\Controllers;

use App\Enum\SentimentEnum;
use App\Http\Requests\GetCommentsByTopicRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FilterTopicResource;
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
            'topic_id' => 'required|exists:topics,id',
            'content' => 'required|string',
        ]);
        // تحليل المشاعر
        $sentiment = $this->sentimentService->analyze($validated['content']);

        // تخزين التعليق في قاعدة البيانات
        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'topic_id' => $validated['topic_id'],
            'content' => $validated['content'],
            'sentiment' => $sentiment,
        ]);

        return response()->json(new CommentResource($comment), 201);
    }

    public function getCommentsByTopic(GetCommentsByTopicRequest $request)
    {
        $topicName = $request->input('topic_name');  // اختياري
        $sentiment = $request->input('sentiment');    // اختياري

        if ($sentiment) {
            $sentiment = SentimentEnum::from($sentiment);  // تحويل النص إلى Enum
        }

        $comments = $this->commentService->getCommentsByFilters($topicName, $sentiment);
        return CommentResource::collection($comments);
    }

    public function getCommentsByUser(Request $request)
    {
        $user = auth()->user();

        $topicId = $request->input('topic_id');
        $sentiment = $request->input('sentiment');

        $query = Comment::where('user_id', $user->id)->with('topic');

        if ($topicId) {
            $query->where('topic_id', $topicId);
        }

        // إضافة الفلتر حسب `sentiment` إذا كان موجوداً
        if ($sentiment) {
            $query->where('sentiment', $sentiment);
        }

        // جلب التعليقات
        $comments = $query->get();

        // إرجاع التعليقات مع المواضيع باستخدام CommentResource
        return CommentResource::collection($comments);
    }

    public function getTopicsWithUserComments()
    {
        $user = auth()->user();

        $topics = Topic::with(['comments' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        $filteredTopics = $topics->filter(function ($topic) {
            return $topic->comments->isNotEmpty();
        });

        return FilterTopicResource::collection($filteredTopics);
    }

}
