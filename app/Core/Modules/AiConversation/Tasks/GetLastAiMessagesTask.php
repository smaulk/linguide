<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\AiConversation\Models\AiMessage;
use Illuminate\Database\Eloquent\Collection;

final class GetLastAiMessagesTask extends Task
{
    /**
     * Возвращает последние сообщения в диалоге
     * @param int $conversationId
     * @param int $limit
     * @return Collection<int, AiMessage>
     */
    public function run(int $conversationId, int $limit = 20): Collection
    {
        return AiMessage::query()
            ->where('conversation_id', $conversationId)
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse();
    }
}