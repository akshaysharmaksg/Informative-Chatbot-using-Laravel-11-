<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ChatController;

class SyncUnansweredQuestions extends Command
{
    protected $signature = 'sync:unanswered';
    protected $description = 'Sync unanswered_questions.json with the database';

    public function handle()
    {
        $chatController = new ChatController();
        $chatController->updateUnansweredQuestionsJson();
        $this->info('Unanswered questions JSON updated successfully.');
    }
}

