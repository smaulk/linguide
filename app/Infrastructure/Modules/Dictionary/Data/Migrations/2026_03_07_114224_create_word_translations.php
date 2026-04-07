<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('word_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id')
                ->constrained('words', 'id')
                ->cascadeOnDelete();
            $table->index('word_id');

            $table->text('text');
            $table->unsignedSmallInteger('rank');
            $table->text('context_en');
            $table->text('context_ru');

            $table->timestampTz('created_at');

            $table->unique(['word_id', 'rank']);
            $table->unique(['word_id', 'text', 'context_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('word_translations');
    }
};
