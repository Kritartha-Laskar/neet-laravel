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
        Schema::create('study_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // e.g. "Class One", "Class Two"
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0); // Serial/Order of class itself
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->foreignId('study_class_id')->nullable()->constrained('study_classes')->onDelete('set null')->after('id');
            $table->integer('sort_order')->default(0)->after('subject'); // Serial of resource within class
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['study_class_id']);
            $table->dropColumn(['study_class_id', 'sort_order']);
        });

        Schema::dropIfExists('study_classes');
    }
};
