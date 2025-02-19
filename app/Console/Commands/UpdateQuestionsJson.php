<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateQuestionsJson extends Command
{
    protected $signature = 'questions:update';
    protected $description = 'Update questions.json file from database';

    public function handle()
    {
        Log::info('Updating questions.json from the database.');

        // Fetch all questions from the database
        $questions = ChatResponse::all();

        if ($questions->isEmpty()) {
            Log::info('No questions found in the database.');
            return;
        }

        // Group questions by category
        $groupedQuestions = [];
        foreach ($questions as $question) {
            $category = $question->category ?? 'Uncategorized';
            if (!isset($groupedQuestions[$category])) {
                $groupedQuestions[$category] = [
                    'title' => $category,
                    'questions' => [],
                ];
            }
            $groupedQuestions[$category]['questions'][] = [
                'question' => $question->question,
                'answer' => $question->answer,
            ];
        }

        // Structure the final JSON data
        $jsonData = [
            'sections' => array_values($groupedQuestions), // Ensuring proper JSON array format
        ];

        // Save the JSON file
        Storage::disk('public')->put('questions.json', json_encode($jsonData, JSON_PRETTY_PRINT));

        Log::info('questions.json has been updated successfully.');
        $this->info('✅ questions.json updated successfully.');
    }
}
