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
            if (!Schema::hasColumn('question_paper', 'paper_type')) {
                $table->enum('paper_type', ['mocktest', 'combined'])->default('combined')->after('exam_name');
            }
            if (!Schema::hasColumn('question_paper', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete()->after('paper_type');
            }
            if (!Schema::hasColumn('question_paper', 'subject_quotas')) {
                $table->json('subject_quotas')->nullable()->after('subject_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_paper', function (Blueprint $table) {
            if (Schema::hasColumn('question_paper', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }
            if (Schema::hasColumn('question_paper', 'paper_type')) {
                $table->dropColumn('paper_type');
            }
            if (Schema::hasColumn('question_paper', 'subject_quotas')) {
                $table->dropColumn('subject_quotas');
            }
        });
    }
};
