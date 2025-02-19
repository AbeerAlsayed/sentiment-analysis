<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterTopicResource extends JsonResource
{
    public function toArray($request)
    {
        $comments = $this->comments->where('user_id', auth()->id());
        if ($comments->isNotEmpty()) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'comments' => $comments,
            ];
        }
        return null;
    }
}
