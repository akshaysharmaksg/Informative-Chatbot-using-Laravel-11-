<?php

namespace App\Http\Controllers;

use App\Models\ChatResponse;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    /**
     * Store a new question and answer in the database and update JSON & cache.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'required|string'
        ]);

        ChatResponse::create($request->only(['question', 'answer', 'category']));

        $this->updateQuestionsJsonAndCache();

        return response()->json(['message' => 'Question added successfully and cache updated']);
    }

    /**
     * Fetch all questions and store them in JSON & cache.
     */
    public function getQuestions()
    {
        return $this->updateQuestionsJsonAndCache();
    }

    /**
     * Update a question and answer by ID and refresh cache.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'required|string'
        ]);

        $question = ChatResponse::find($id);
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $question->update($request->only(['question', 'answer', 'category']));

        $this->updateQuestionsJsonAndCache();

        return response()->json(['message' => 'Question updated successfully']);
    }

    /**
     * Delete a question by ID and refresh cache.
     */
    public function delete($id)
    {
        $question = ChatResponse::find($id);
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $question->delete();
        $this->updateQuestionsJsonAndCache();

        return response()->json(['message' => 'Question deleted successfully']);
    }

    /**
     * Handle user question input and return the answer.
     */
    public function getAnswer(Request $request)
    {
        $request->validate(['question' => 'required|string']);
        $userQuestion = trim(strtolower($request->input('question')));
    
        // Fetch cached index or query database
        $indexCache = Cache::get('chatbot_index', []);
    
        // Check if question exists in cache index
        if (isset($indexCache[$userQuestion])) {
            return response()->json([
                'question' => $userQuestion,
                'answer' => $indexCache[$userQuestion]
            ]);
        }
    
        // Fetch all stored questions from DB if not in cache
        $questions = ChatResponse::pluck('question', 'id');
    
        $bestMatchId = null;
        $shortestDistance = PHP_INT_MAX;
    
        foreach ($questions as $id => $storedQuestion) {
            $storedQuestionLower = trim(strtolower($storedQuestion));
    
            $distance = levenshtein($userQuestion, $storedQuestionLower);
    
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $bestMatchId = $id;
            }
        }
    
        $threshold = 5; 
    
        if ($bestMatchId !== null && $shortestDistance <= $threshold) {
            $bestMatch = ChatResponse::find($bestMatchId);
    
            return response()->json([
                'question' => $bestMatch->question,
                'answer' => $bestMatch->answer
            ]);
        }
    
        // Log unanswered question
        $unanswered = UnansweredQuestion::updateOrCreate(
            ['question' => $userQuestion],
            ['times_asked' => \DB::raw('times_asked + 1')]
        );
    
        $this->updateUnansweredQuestionsJson();

        return response()->json([
            'question' => $userQuestion,
            'answer' => "Sorry, I don't have an answer yet!"
        ]);
    }

    /**
     * Update questions.json and cache to keep everything in sync.
     */
    private function updateQuestionsJsonAndCache()
    {
        Log::info('Updating questions.json and syncing cache...');

        $questions = ChatResponse::all();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No questions found'], 404);
        }

        $groupedQuestions = [];
        $indexCache = [];

        foreach ($questions as $question) {
            $groupedQuestions[$question->category][] = [
                'question' => $question->question,
                'answer' => $question->answer
            ];
            $indexCache[strtolower(trim($question->question))] = $question->answer;
        }

        $jsonData = ['sections' => []];
        foreach ($groupedQuestions as $category => $qList) {
            $jsonData['sections'][] = [
                'title' => $category,
                'questions' => $qList
            ];
        }

        Cache::put('chatbot_json', $jsonData, now()->addHours(6));
        Cache::put('chatbot_index', $indexCache, now()->addHours(6));

        Storage::disk('public')->put('questions.json', json_encode($jsonData, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'questions.json and cache updated successfully']);
    }

    /**
     * Update unanswered_questions.json and keep it in sync.
     */
    public function updateUnansweredQuestionsJson()
{
    Log::info('Syncing unanswered_questions.json with the database...');

    $unansweredQuestions = UnansweredQuestion::all();

    $jsonData = ['unanswered_questions' => []];
    foreach ($unansweredQuestions as $question) {
        $jsonData['unanswered_questions'][] = [
            'id' => $question->id,
            'question' => $question->question,
            'times_asked' => $question->times_asked
        ];
    }

    // Save the updated JSON file
    Storage::disk('public')->put('unanswered_questions.json', json_encode($jsonData, JSON_PRETTY_PRINT));

    Log::info('Updated unanswered_questions.json successfully.');
}


    private function syncJsonToUnansweredQuestions()
{
    Log::info('Syncing database with unanswered_questions.json...');

    $jsonPath = storage_path('app/public/unanswered_questions.json');

    if (!file_exists($jsonPath)) {
        Log::error('unanswered_questions.json file not found.');
        return;
    }

    $jsonData = json_decode(file_get_contents($jsonPath), true);

    if (!$jsonData || !isset($jsonData['unanswered_questions'])) {
        Log::error('Invalid JSON structure in unanswered_questions.json.');
        return;
    }

    foreach ($jsonData['unanswered_questions'] as $jsonQuestion) {
        UnansweredQuestion::updateOrCreate(
            ['question' => $jsonQuestion['question']],
            ['times_asked' => $jsonQuestion['times_asked']]
        );
    }

    Log::info('Database successfully synced with unanswered_questions.json.');
}

public function syncUnansweredFromJson()
{
    $this->syncJsonToUnansweredQuestions();
    return response()->json(['message' => 'Database updated from unanswered_questions.json']);
}

public function updateQuestionsJson()
{
    $filePath = storage_path('app/public/questions.json');

    $questions = ChatResponse::all();
    $data = [
        "sections" => [
            [
                "title" => "FAQs",
                "questions" => $questions->map(function ($q) {
                    return [
                        "question" => $q->question,
                        "answer" => $q->answer
                    ];
                })->toArray()
            ]
        ]
    ];

    File::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

}
