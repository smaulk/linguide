<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\Ai\Actions\AskAiAction;
use App\Core\Modules\Ai\Dto\AskAiDto;
use App\Core\Modules\Ai\Dto\AskConversationAiDto;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class AskAiHandler extends Handler
{
    public function __construct(private readonly AskAiAction $action){}

    public function __invoke(Nutgram $bot, string $message): void
    {
        $response = $this->action->run(
            new AskAiDto(AiAgentType::ENGLISH_TALK, $message)
        );

        $bot->sendMessage($response->content);
    }
}