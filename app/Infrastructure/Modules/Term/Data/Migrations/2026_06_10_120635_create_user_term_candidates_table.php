<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_term_candidates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            $table->foreignId('candidate_id')
                ->constrained('term_candidates', 'id')
                ->cascadeOnDelete();

            $table->boolean('is_processed')->default(false);

            $table->timestampsTz();

            $table->unique(['user_id', 'candidate_id']);
            $table->index(['is_processed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_term_candidates');
    }
};
