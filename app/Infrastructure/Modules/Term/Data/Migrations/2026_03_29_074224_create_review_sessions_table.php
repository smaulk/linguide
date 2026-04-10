<?php

use App\Core\Modules\Term\Enums\ReviewSessionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            $table->enum('status', ReviewSessionStatus::values())
                ->default(ReviewSessionStatus::ACTIVE->name);

            $table->timestampTz('started_at');
            $table->timestampTz('finished_at')->nullable();
            $table->timestampTz('updated_at')->nullable();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_sessions');
    }
};
