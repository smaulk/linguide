<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('word_review_session_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('session_id')
                ->constrained('word_review_sessions', 'id')
                ->cascadeOnDelete();

            $table->foreignId('word_id')
                ->constrained('words', 'id')
                ->cascadeOnDelete();

            $table->boolean('is_correct')->nullable();

            $table->timestampTz('presented_at')->nullable();
            $table->timestampTz('answered_at')->nullable();

            $table->unique(['session_id', 'word_id']);
            $table->index(['session_id', 'answered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('word_review_session_items');
    }
};
