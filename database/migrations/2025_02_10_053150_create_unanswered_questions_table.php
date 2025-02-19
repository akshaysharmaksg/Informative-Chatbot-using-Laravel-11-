<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('unanswered_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question')->unique();
            $table->integer('times_asked')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unanswered_questions');
    }
};
