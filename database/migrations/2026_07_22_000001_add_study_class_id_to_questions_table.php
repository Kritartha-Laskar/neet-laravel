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
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'study_class_id')) {
                $table->foreignId('study_class_id')->nullable()->after('subject_id')->constrained('study_classes')->onDelete('set null');
            }
            if (!Schema::hasColumn('questions', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('question_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'study_class_id')) {
                $table->dropForeign(['study_class_id']);
                $table->dropColumn('study_class_id');
            }
            if (Schema::hasColumn('questions', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
