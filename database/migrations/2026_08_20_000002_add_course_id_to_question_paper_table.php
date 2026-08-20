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
            if (!Schema::hasColumn('question_paper', 'course_id')) {
                $table->foreignId('course_id')->nullable()->constrained('course_names')->nullOnDelete()->after('exam_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_paper', function (Blueprint $table) {
            if (Schema::hasColumn('question_paper', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
        });
    }
};
