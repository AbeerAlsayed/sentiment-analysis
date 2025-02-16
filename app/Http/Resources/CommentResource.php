<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'topic' => [
                'id' => $this->topic->id,
                'name' => $this->topic->name,
                'image' => $this->topic->image,  // إضافة الصورة هنا
            ],
            'sentiment' => $this->sentiment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
