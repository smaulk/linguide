<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\AiConversation\Enums\AiConversationMode;
use App\Core\Modules\AiConversation\Models\AiConversation;
use App\Core\Modules\User\Models\User;
use Throwable;

final class CreateAiConversationAction extends Action
{
    /**
     * @throws Throwable
     */
    public function run(int $userId, AiConversationMode $mode): int
    {
        $user = $this->getUserById($userId);
        $conversation = new AiConversation();
        $conversation->user_id = $user->id;
        $conversation->mode = $mode;
        $conversation->saveOrFail();

        return $conversation->id;
    }

    private function getUserById(int $userId): User
    {
        return User::query()->findOrFail($userId);
    }
}