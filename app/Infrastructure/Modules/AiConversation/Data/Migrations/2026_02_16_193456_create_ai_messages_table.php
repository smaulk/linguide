<?php

use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('ai_conversations', 'id')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('telegram_message_id')->nullable();
            $table->enum('role', AiMessageRole::values());
            $table->text('content');
            $table->jsonb('meta')->nullable();
            $table->timestampsTz();

            $table->index(['conversation_id', 'id'], 'ai_messages_conversation_id_id');
            $table->unique(['telegram_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};
