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

        $sentiment = $this->sentimentService->analyze($validated['content']);

        $comment = $this->commentService->createComment(
            auth()->user()->id,
            $validated['topic_id'],
            $validated['content'],
            $sentiment
        );

        return response()->json(new CommentResource($comment), 201);
    }

    public function getCommentsByTopic(GetCommentsByTopicRequest $request)
    {
        $topicName = $request->input('topic_name');
        $sentiment = $request->input('sentiment');

        if ($sentiment) {
            $sentiment = SentimentEnum::from($sentiment);
        }
        if (!$topicName) {
            $topic = Topic::first();
            $topicName = $topic ? $topic->name : null;
        }
        $comments = $this->commentService->getCommentsByFilters($topicName, $sentiment);
        return CommentResource::collection($comments);
    }

    public function getTopicsWithUserComments()
    {
        $topics = $this->commentService->getTopicsWithUserComments(auth()->user());

        return FilterTopicResource::collection($topics);
    }

}
