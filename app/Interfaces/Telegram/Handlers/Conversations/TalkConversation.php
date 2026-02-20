<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers\Conversations;

use App\Core\Modules\Ai\Actions\AskConversationAiAction;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use App\Core\Modules\Ai\Dto\AskConversationAiDto;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\AiConversation\Actions\CreateAiConversationAction;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Dto\CreateAiMessagesDto;
use App\Core\Modules\AiConversation\Enums\AiConversationMode;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use App\Core\Modules\AiConversation\Tasks\CreateAiMessagesTask;
use App\Interfaces\Telegram\Commands\AiTalkCommand;
use App\Interfaces\Telegram\Keyboards\Reply\AiTalkReplyKeyboard;
use App\Interfaces\Telegram\Keyboards\Reply\MainMenuReplyKeyboard;
use App\Interfaces\Telegram\Parents\Conversation;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Message\Message;
use Throwable;

final class TalkConversation extends Conversation
{
    public int $conversationId;

    public function __construct(
        private readonly CreateAiConversationAction $createConversationAction,
        private readonly AskConversationAiAction $askConversationAction,
        private readonly MainMenuReplyKeyboard $mainMenuKeyboard,
        private readonly AiTalkReplyKeyboard $talkKeyboard,
    ){}

    /**
     * @throws Throwable
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $this->conversationId = $this->createAiConversation($this->getAppUserId($bot));
        $this->sendMessage($bot, 'Вы вошли в режим диалога с ИИ!');
        $this->next('sendMessageAi');
    }

    /**
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function sendMessageAi(Nutgram $bot): void
    {
        $message = $bot->message();
        if ($message === null) {
            return;
        }
        $messageText = $message->getText();
        if ($messageText === null) {
            return;
        }

        if ($this->checkEndMessage($messageText)) {
            $bot->sendMessage(
                text: 'Вы вышли из режима диалога с ИИ!',
                reply_markup: $this->mainMenuKeyboard->make(),
            );
            $this->end();
            return;
        }

        $aiResponse = $this->askConversationAi($messageText, $message->message_id);
        $this->sendMessage($bot, $aiResponse->content);
    }

    /**
     * @throws Throwable
     */
    private function createAiConversation(int $appUserId): int
    {
        return $this->createConversationAction->run(
            userId: $appUserId,
            mode: AiConversationMode::TALKING,
        );
    }

    private function askConversationAi(string $content, int $messageId): AiMessageDto
    {
        return $this->askConversationAction->run(
            new AskConversationAiDto(
                conversationId: $this->conversationId,
                content: $content,
                tgMessageId: $messageId,
            ),
        );
    }

    private function sendMessage(Nutgram $bot, string $message): void
    {
        $bot->sendMessage(
            text: $message,
            reply_markup: $this->talkKeyboard->make(),
        );
    }

    private function checkEndMessage(string $message): bool
    {
        return trim(mb_strtolower($message)) === mb_strtolower(AiTalkCommand::STOP_TALK->value);
    }
}