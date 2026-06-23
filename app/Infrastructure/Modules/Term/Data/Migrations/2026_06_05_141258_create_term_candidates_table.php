<?php

use App\Core\Modules\Term\Enums\TermCandidateStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_candidates', function (Blueprint $table) {
            $table->id();
            $table->text('raw_term');

            $table->enum('status', TermCandidateStatus::values())
                ->default(TermCandidateStatus::PENDING->value);

            $table->foreignId('term_id')
                ->nullable()
                ->constrained('terms', 'id')
                ->nullOnDelete();

            $table->timestampsTz();

            $table->unique(['raw_term']);
            $table->index(['term_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_candidates');
    }
};
