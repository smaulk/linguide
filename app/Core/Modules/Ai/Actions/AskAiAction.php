<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Dto\AskAiDto;
use App\Core\Modules\Ai\Factories\AiAgentFactory;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;

final class AskAiAction extends Action
{
    public function __construct(private readonly AiAgentFactory $agentFactory){}

    public function run(AskAiDto $dto): AiMessageDto
    {
        $agent = $this->agentFactory->make($dto->agentType);
        $userMessage = $this->makeUserMessage($dto);

        return $this->askAgent($agent, $userMessage);
    }

    private function makeUserMessage(AskAiDto $dto): AiMessageDto
    {
        return new AiMessageDto(
            role: AiMessageRole::USER,
            content: $dto->content,
            meta: $dto->meta,
        );
    }

    private function askAgent(AiAgentContract $agent, AiMessageDto $message): AiMessageDto
    {
        $response = $agent->send($message);

        return new AiMessageDto(
            role: AiMessageRole::ASSISTANT,
            content: $response->text,
        );
    }
}