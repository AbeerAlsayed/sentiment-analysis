<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * تخصيص كيفية عرض الاستثناءات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        // تخصيص التعامل مع ModelNotFoundException الخاصة بـ Topic
        if ($e instanceof ModelNotFoundException) {
            if ($e->getModel() == 'App\Models\Topic') {
                // تخصيص رسالة الخطأ الخاصة بالـ Topic
                return response()->json([
                    'message' => 'The topic you are looking for does not exist.'
                ], 404);
            }

            // إذا كان من نوع آخر (أو أي موديل آخر)
            return response()->json([
                'message' => 'The requested resource was not found.'
            ], 404);
        }

        // التعامل مع استثناءات أخرى باستخدام الدالة الأصلية
        return parent::render($request, $e);
    }
}
