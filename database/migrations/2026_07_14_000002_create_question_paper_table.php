<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_paper', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('exam_name')->default('NEET');   // e.g. NEET, JEE
            $table->integer('total_questions')->default(180);
            $table->integer('duration_minutes')->default(180);
            $table->integer('total_marks')->default(720);
            $table->year('exam_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_paper');
    }
};
