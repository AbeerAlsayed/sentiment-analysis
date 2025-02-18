<?php

namespace App\Imports;

use App\Models\Sentiment;
use Maatwebsite\Excel\Concerns\ToModel;

class SentimentDataImport implements ToModel
{
    public function model(array $row)
    {
        return new Sentiment([
            'sentiment' => $row[0],  // 0 = سالب، 4 = إيجابي
            'text' => $row[5],       // النص
        ]);
    }
}
