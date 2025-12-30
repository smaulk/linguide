<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\Ai\Actions\AskAiAction;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use App\Core\Modules\Ai\Dto\AskAiDto;
use App\Core\Modules\Ai\Enums\AiDriverType;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

final class AskAiHandler extends Handler
{
    protected function handle(Nutgram $bot, ...$parameters): ?Message
    {
        [$message] = $parameters;
        $response = $this->getAiResponse($message);

        return $bot->sendMessage($response->text);
    }

    private function getAiResponse(string $message): AiResponseDto
    {
        return $this->action(AskAiAction::class)->run(
            new AskAiDto(
                message: $message,
                providerType: AiDriverType::GEMINI
            ),
        );
    }
}