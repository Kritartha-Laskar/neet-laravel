<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('question_paper', function (Blueprint $table) {
            if (!Schema::hasColumn('question_paper', 'chapter_id')) {
                $table->foreignId('chapter_id')->nullable()->after('subject_id')->constrained('chapters')->onDelete('set null');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'chapter_id')) {
                $table->foreignId('chapter_id')->nullable()->after('subject_id')->constrained('chapters')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_paper', function (Blueprint $table) {
            if (Schema::hasColumn('question_paper', 'chapter_id')) {
                $table->dropForeign(['chapter_id']);
                $table->dropColumn('chapter_id');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'chapter_id')) {
                $table->dropForeign(['chapter_id']);
                $table->dropColumn('chapter_id');
            }
        });
    }
};
