<?php

use App\Core\Modules\Term\Enums\PartOfSpeech;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('term_id')
                ->constrained('terms', 'id')
                ->cascadeOnDelete();

            $table->enum('pos', PartOfSpeech::values());
            $table->unsignedSmallInteger('level')->nullable();

            $table->timestampTz('created_at');

            $table->unique(['term_id', 'pos']);
            $table->index(['pos', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_variants');
    }
};
