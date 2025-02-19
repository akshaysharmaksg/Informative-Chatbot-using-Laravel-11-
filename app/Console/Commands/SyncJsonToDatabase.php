<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ChatResponse;

class SyncJsonToDatabase extends Command
{
    protected $signature = 'questions:sync';
    protected $description = 'Sync questions.json with the MySQL database';

    public function handle()
    {
        $filePath = base_path('storage/app/public/questions.json');

        if (!file_exists($filePath)) {
            $this->error("❌ JSON file not found at: $filePath");
            return;
        }

        $jsonContent = file_get_contents($filePath);

        // Log and display JSON content for debugging
        Log::info("JSON Content: " . $jsonContent);

        $data = json_decode($jsonContent, true);

        // Check if JSON is properly decoded
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ JSON Decoding Error: " . json_last_error_msg());
            return;
        }

        if (!$data || !isset($data['sections'])) {
            $this->error("❌ Invalid JSON format! Make sure 'sections' key exists.");
            return;
        }

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->error("❌ Database connection error: " . $e->getMessage());
            return;
        }

        foreach ($data['sections'] as $section) {
            $category = $section['title'] ?? 'Uncategorized'; // Default category if not provided

            foreach ($section['questions'] as $entry) {
                ChatResponse::updateOrCreate(
                    ['question' => $entry['question']], // Unique identifier
                    [
                        'answer' => $entry['answer'],
                        'category' => $category // Store title as category
                    ]
                );
            }
        }

        $this->info("✅ Database successfully updated from JSON!");
    }
}
