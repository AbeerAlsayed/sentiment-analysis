<?php

namespace App\Http\Requests;

use App\Enum\SentimentEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetCommentsByTopicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'topic_name' => 'nullable|string',  // `topic_name` اختياري
            'sentiment' => 'nullable|in:positive,negative',  // `sentiment` اختياري ويمكن أن يكون إما 'positive' أو 'negative'
        ];
    }

    public function messages()
    {
        return [
            'sentiment.in' => 'The sentiment must be either "positive" or "negative". Please check your input.', // رسالة الخطأ المخصصة
        ];
    }
}
