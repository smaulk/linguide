<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('term_variants', 'id')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('repetitions')->default(0);
            $table->unsignedInteger('interval')->default(0);
            $table->float('ease_factor');

            $table->timestampTz('due_at')->index();
            $table->timestampTz('last_reviewed_at')->nullable();

            $table->timestampsTz();

            $table->unique(['user_id', 'variant_id']);
            $table->index(['user_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_progress');
    }
};
