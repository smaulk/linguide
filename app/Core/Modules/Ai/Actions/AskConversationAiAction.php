<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Dto\AskConversationAiDto;
use App\Core\Modules\Ai\Factories\AiAgentFactory;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Dto\CreateAiMessagesDto;
use App\Core\Modules\AiConversation\Enums\AiConversationMode;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use App\Core\Modules\AiConversation\Mappers\AiMessageMapper;
use App\Core\Modules\AiConversation\Models\AiConversation;
use App\Core\Modules\AiConversation\Tasks\CreateAiMessagesTask;
use App\Core\Modules\AiConversation\Tasks\GetLastAiMessagesTask;
use Throwable;

final class AskConversationAiAction extends Action
{
    public function __construct(
        private readonly AiAgentFactory $agentFactory,
        private readonly GetLastAiMessagesTask $getLastMessagesTask,
        private readonly CreateAiMessagesTask $createMessagesTask,
        private readonly AiMessageMapper $messageMapper,
    ){}

    /**
     * @throws Throwable
     */
    public function run(AskConversationAiDto $dto): AiMessageDto
    {
        $conversation = $this->getConversation($dto->conversationId);
        $agent = $this->makeAgent($conversation->mode);

        $userMessage = $this->makeUserMessage($dto);
        $assistantMessage = $this->askAgent($agent, $conversation->id, $userMessage);
        $this->saveMessages($conversation->id, [$userMessage, $assistantMessage]);

        return $assistantMessage;
    }

    private function getConversation(int $conversationId): AiConversation
    {
        return AiConversation::query()
            ->select(['id', 'mode'])
            ->findOrFail($conversationId);
    }

    private function makeAgent(AiConversationMode $mode): AiAgentContract
    {
        return $this->agentFactory->make($mode->agentType());
    }

    private function askAgent(AiAgentContract $agent, int $conversationId, AiMessageDto $userMessage): AiMessageDto
    {
        $messages = $this->getLastMessages($conversationId, $agent->getHistoryLimit());
        $messages[] = $userMessage;
        $response = $agent->send($messages);

        return new AiMessageDto(
            role: AiMessageRole::ASSISTANT,
            content: $response->text,
        );
    }

    /**
     * @return AiMessageDto[]
     */
    private function getLastMessages(int $conversationId, int $historyLimit): array
    {
        $messages = $this->getLastMessagesTask->run($conversationId, $historyLimit);
        return $this->messageMapper->mapModelsToDtoArray($messages);
    }

    private function makeUserMessage(AskConversationAiDto $dto): AiMessageDto
    {
        return new AiMessageDto(
            role: AiMessageRole::USER,
            content: $dto->content,
            tgMessageId: $dto->tgMessageId,
            meta: $dto->meta,
        );
    }

    /**
     * @param AiMessageDto[] $messages
     * @throws Throwable
     */
    private function saveMessages(int $conversationId, array $messages): void
    {
        $this->createMessagesTask->run(
            new CreateAiMessagesDto($conversationId, $messages)
        );
    }
}