<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_examples', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_id')
                ->constrained('translations', 'id')
                ->cascadeOnDelete();

            $table->text('sentence_en');
            $table->text('sentence_ru');

            $table->timestampTz('created_at');

            $table->index(['translation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_examples');
    }
};
