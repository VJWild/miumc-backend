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
        Schema::create('av_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('av_assignments');
            $table->foreignId('classroom_user_id')->constrained('av_classroom_user');
            $table->text('content')->nullable();
            $table->string('file_path')->unique()->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->decimal('grade');
            $table->text('teacher_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('av_submissions');
    }
};
