<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')
                ->constrained('term_variants', 'id')
                ->cascadeOnDelete();

            $table->text('text');
            $table->text('context_en');
            $table->text('context_ru');

            $table->timestampTz('created_at');

            $table->unique(['variant_id', 'text', 'context_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
