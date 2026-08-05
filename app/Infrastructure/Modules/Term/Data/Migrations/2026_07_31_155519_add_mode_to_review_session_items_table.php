<?php

use App\Core\Modules\Term\Enums\ReviewMode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_session_items', function (Blueprint $table) {
            $table->enum('mode', ReviewMode::values())
                ->default(ReviewMode::EnglishToRussian->value);
        });
    }

    public function down(): void
    {
        Schema::table('review_session_items', function (Blueprint $table) {
            $table->dropColumn('mode');
        });
    }
};
