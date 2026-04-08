<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->index('user_id');

            $table->unsignedTinyInteger('level')->nullable();
            $table->smallInteger('utc_offset')->nullable();
            $table->unsignedInteger('review_limit');

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
