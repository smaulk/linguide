<?php

use App\Core\Modules\Dictionary\Enums\TermType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();

            $table->text('text');
            $table->enum('type', TermType::values());
            $table->boolean('is_verified')->default(false);

            $table->timestampsTz();

            $table->unique(['text']);
            $table->index(['is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
