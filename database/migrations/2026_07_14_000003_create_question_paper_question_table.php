<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_paper_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_paper_id')
                  ->constrained('question_paper')
                  ->onDelete('cascade');
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onDelete('cascade');
            $table->unsignedSmallInteger('order')->default(0); // display order in paper
            $table->unsignedTinyInteger('marks')->default(4);  // marks per question
            $table->timestamps();

            // A question can appear in a paper only once
            $table->unique(['question_paper_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_paper_question');
    }
};
