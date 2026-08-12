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
        Schema::table('resources', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('study_class_id')->constrained('course_names')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->after('course_id')->constrained('subjects')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['resources_subject_id_foreign']);
            $table->dropForeign(['resources_course_id_foreign']);
            $table->dropColumn(['course_id', 'subject_id']);
        });
    }
};
