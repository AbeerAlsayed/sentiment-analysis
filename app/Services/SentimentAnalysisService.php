<?php

namespace App\Services;

use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TfidfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;

class SentimentAnalysisService
{
    protected $model;
    protected $vectorizer;
    protected $tfidfTransformer;
    protected $modelPath;

    public function __construct()
    {
        $this->modelPath = storage_path('app/model.dat');

        $this->vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
        $this->tfidfTransformer = new TfidfTransformer();

        // تحميل النموذج إذا كان موجودًا، وإلا تدريبه
        if (file_exists($this->modelPath)) {
            $this->model = unserialize(file_get_contents($this->modelPath));
        } else {
            $this->trainModel();
        }
    }

    public function trainModel()
    {
        $comments = [];
        $labels = [];

        $filePath = storage_path('app/datasets/sentiment140.csv');

        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = true;
            while (($line = fgetcsv($handle)) !== false) {
                if ($firstLine) {
                    $firstLine = false;
                    continue;
                }

                if (!isset($line[1], $line[10]) || empty(trim($line[10]))) {
                    continue;
                }

                $sentiment = strtolower(trim($line[1]));
                $commentText = $this->cleanText(trim($line[10]));

                if ($sentiment === 'positive') {
                    $labels[] = 'positive';
                } elseif ($sentiment === 'negative') {
                    $labels[] = 'negative';
                } else {
                    continue;
                }

                $comments[] = $commentText;
            }
            fclose($handle);
        } else {
            throw new \Exception("Unable to open file at: " . $filePath);
        }

        if (empty($comments)) {
            throw new \Exception("No valid comments found in dataset.");
        }

        $this->vectorizer->fit($comments);
        $this->vectorizer->transform($comments);
        $this->tfidfTransformer->fit($comments);
        $this->tfidfTransformer->transform($comments);

        $this->model = new NaiveBayes();
        $this->model->train($comments, $labels);

        file_put_contents($this->modelPath, serialize($this->model));
    }

    public function analyze($commentText)
    {
        if (!$this->model) {
            throw new \Exception("Model is not trained yet.");
        }

        $transformedComment = [$this->cleanText($commentText)];
        $this->vectorizer->transform($transformedComment);
        $this->tfidfTransformer->transform($transformedComment);

        return $this->model->predict($transformedComment)[0];
    }

    private function cleanText($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/@\w+/', '', $text);
        $text = preg_replace('/https?:\/\/\S+/', '', $text);
        $text = preg_replace('/[^a-z\s]/', '', $text);
        return trim($text);
    }
}
