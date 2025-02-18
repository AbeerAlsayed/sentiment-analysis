<?php

namespace App\Services;

use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;

class SentimentAnalysisService
{
    protected $model;
    protected $vectorizer;

    public function __construct()
    {
        $this->vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
        $this->trainModel();
    }

    public function trainModel()
    {
        $comments = [];
        $labels = [];

        // فتح ملف CSV
        $filePath = storage_path('app/datasets/sentiment140.csv'); // تأكد من المسار الصحيح للملف

        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = true;
            while (($line = fgetcsv($handle)) !== false) {
                if ($firstLine) {
                    $firstLine = false;
                    continue;
                }

                // التحقق من أن الأعمدة المطلوبة موجودة
                if (!isset($line[1], $line[10]) || empty(trim($line[10]))) {
                    continue; // تخطي الصفوف غير الصالحة
                }

                // الحصول على التصنيف والنص من العمودين الصحيحين
                $sentiment = strtolower(trim($line[1])); // التصنيف موجود في العمود الثاني
                $commentText = trim($line[10]); // النص موجود في العمود 11

                // تخطي التعليقات المحايدة، والاحتفاظ فقط بالإيجابية والسلبية
                if ($sentiment === 'positive') {
                    $labels[] = 'positive';
                } elseif ($sentiment === 'negative') {
                    $labels[] = 'negative';
                } else {
                    continue; // تخطي البيانات المحايدة
                }

                $comments[] = $commentText;
            }
            fclose($handle);
        } else {
            throw new \Exception("Unable to open file at: " . $filePath);
        }

        // التحقق من أن هناك بيانات كافية
        if (empty($comments)) {
            throw new \Exception("No valid comments found in dataset.");
        }

        // 1. تدريب الـ Vectorizer على النصوص
        $this->vectorizer->fit($comments);

        // 2. تحويل النصوص إلى تمثيل رقمي
        $commentsTransformed = $comments;
        $this->vectorizer->transform($commentsTransformed);

        // التأكد من أن البيانات قد تحولت بنجاح
        if (empty($commentsTransformed)) {
            throw new \Exception("No transformed data available for training.");
        }

        // تدريب نموذج NaiveBayes باستخدام البيانات
        $this->model = new NaiveBayes();
        $this->model->train($commentsTransformed, $labels);
    }

    public function analyze($commentText)
    {
        // التحقق من أن النموذج قد تم تدريبه
        if (!$this->model) {
            throw new \Exception("Model is not trained yet.");
        }

        // 1. تحويل النص إلى قيم عددية باستخدام نفس الـ Vectorizer المدرب
        $transformedComment = [$commentText];
        $this->vectorizer->transform($transformedComment);

        // التأكد من أن البيانات قد تحولت بنجاح
        if (empty($transformedComment[0])) {
            throw new \Exception("Error transforming the input comment.");
        }

        // 2. تصنيف النص باستخدام النموذج المدرب
        return $this->model->predict($transformedComment)[0];
    }

    public function evaluateModel()
    {
        $testComments = [
            "I love this product, it's amazing!",  // إيجابي
            "I hate this, it's the worst experience ever.",  // سلبي
            "Not bad, but could be better.",  // محايد (لكن النموذج لن يعرف المحايد)
            "Fantastic service! Highly recommended.",  // إيجابي
            "Terrible quality, I regret buying it.",  // سلبي
        ];

        $expectedLabels = ['positive', 'negative', 'positive', 'positive', 'negative'];

        $correct = 0;
        foreach ($testComments as $index => $comment) {
            $prediction = $this->analyze($comment);
            echo "Comment: \"$comment\" -> Predicted: $prediction, Expected: " . $expectedLabels[$index] . "\n";

            if ($prediction === $expectedLabels[$index]) {
                $correct++;
            }
        }

        $accuracy = ($correct / count($testComments)) * 100;
        echo "Model Accuracy: $accuracy% \n";
    }
}
