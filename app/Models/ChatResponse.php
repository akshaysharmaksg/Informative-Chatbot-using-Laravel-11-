<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\ChatController;

class ChatResponse extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = ['question', 'answer', 'category'];

    // Define the events that should trigger updating the JSON file
    protected static function booted()
    {
        static::created(function ($chatResponse) {
            (new ChatController)->updateQuestionsJson(); // Automatically update JSON after creating
        });

        static::updated(function ($chatResponse) {
            (new ChatController)->updateQuestionsJson(); // Automatically update JSON after updating
        });

        static::deleted(function ($chatResponse) {
            (new ChatController)->updateQuestionsJson(); // Automatically update JSON after deleting
        });
    }
}
