<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
Route::get('/chat', function () {
    return view('chat');
})->name('chat');

Route::post('/chat/response', [ChatController::class, 'getResponse']);
Route::get('/chat/questions', [ChatController::class, 'getQuestions']);
Route::get('/chat/questions-file', [ChatController::class, 'getSavedQuestions']);
Route::post('/chat/store', [ChatController::class, 'store']);
Route::put('/chat/update/{id}', [ChatController::class, 'update']);
Route::delete('/chat/delete/{id}', [ChatController::class, 'delete']);
Route::post('/questions/store', [ChatController::class, 'store']);
Route::get('/chat/sync-unanswered', [ChatController::class, 'syncUnansweredFromJson']);
