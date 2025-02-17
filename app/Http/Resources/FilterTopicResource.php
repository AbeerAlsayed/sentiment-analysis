<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterTopicResource extends JsonResource
{
    public function toArray($request)
    {
        // التحقق إذا كان المستخدم قد قام بالتعليق على هذا الموضوع
        $comments = $this->comments->where('user_id', auth()->id());

        // إذا كان هناك تعليقات من المستخدم على هذا الموضوع، سيتم إرجاع الموضوع مع التعليقات
        if ($comments->isNotEmpty()) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'comments' => $comments,  // إرجاع التعليقات التي تخص المستخدم
            ];
        }

        // إذا لم يقم المستخدم بالتعليق على هذا الموضوع، يتم تجاهله
        return null;
    }
}
