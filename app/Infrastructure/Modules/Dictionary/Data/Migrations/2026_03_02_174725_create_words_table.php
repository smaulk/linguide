<?php

use App\Core\Modules\Dictionary\Enums\PartOfSpeech;
use App\Core\Modules\User\Enums\LanguageLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();

            $table->text('text');
            $table->enum('pos', PartOfSpeech::values());
            $table->unsignedTinyInteger('level');

            $table->timestampTz('created_at');

            $table->unique(['text', 'pos']);
            $table->index(['level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
