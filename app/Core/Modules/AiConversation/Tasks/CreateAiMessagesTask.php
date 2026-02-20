<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Tasks;

use App\Core\Common\Parents\Action;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Dto\CreateAiMessagesDto;
use App\Core\Modules\AiConversation\Models\AiConversation;
use App\Core\Modules\AiConversation\Models\AiMessage;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class CreateAiMessagesTask extends Action
{
    /**
     * @throws Throwable
     */
    public function run(CreateAiMessagesDto $dto): void
    {
        if (empty($dto->messages)) {
            return;
        }

        DB::transaction(function () use ($dto) {
            $conversation = $this->getConversation($dto->conversationId);
            $now = now()->toImmutable();

            $rows = [];
            foreach ($dto->messages as $message) {
                $rows[] = $this->mapMessage($message, $conversation->id, $now);
            }

            $result = AiMessage::query()->insert($rows);
            if (!$result) {
                throw new RuntimeException('Failed to insert AI messages');
            }
        });
    }

    private function getConversation(int $conversationId): AiConversation
    {
        // Делаем блокировку для исключения гонки
        return AiConversation::query()
            ->whereKey($conversationId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapMessage(AiMessageDto $message, int $conversationId, CarbonInterface $time): array
    {
        return [
            'conversation_id'     => $conversationId,
            'telegram_message_id' => $message->tgMessageId,
            'role'                => $message->role->value,
            'content'             => $message->content,
            'meta'                => $message->meta,
            'created_at'          => $time,
            'updated_at'          => $time,
        ];
    }
}