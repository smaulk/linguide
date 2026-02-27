<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers\Conversations;

use App\Core\Modules\Ai\Actions\AskConversationAiAction;
use App\Core\Modules\Ai\Dto\AskConversationAiDto;
use App\Core\Modules\AiConversation\Actions\CreateAiConversationAction;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Enums\AiConversationMode;
use App\Interfaces\Telegram\Commands\AiTalkCommand;
use App\Interfaces\Telegram\Keyboards\Reply\AiTalkReplyKeyboard;
use App\Interfaces\Telegram\Keyboards\Reply\MainMenuReplyKeyboard;
use App\Interfaces\Telegram\Parents\Conversation;
use App\Interfaces\Telegram\Response\Markdown\MarkdownSender;
use League\CommonMark\Exception\CommonMarkException;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatAction;
use Throwable;

final class TalkConversation extends Conversation
{
    public int $conversationId;

    public function __construct(
        private readonly CreateAiConversationAction $createConversationAction,
        private readonly AskConversationAiAction $askConversationAction,
        private readonly MarkdownSender $markdownSender,
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
        $bot->sendMessage(
            text: 'Вы вошли в режим диалога с ИИ.',
            reply_markup: $this->talkKeyboard->make(),
        );
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
                text: 'Вы вышли из режима диалога с ИИ.',
                reply_markup: $this->mainMenuKeyboard->make(),
            );
            $this->end();
            return;
        }

        $this->sendTyping($bot);
        $aiResponse = $this->askConversationAi($messageText, $message->message_id);
        $this->sendMarkdownMessage($bot, $aiResponse->content);
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

    /**
     * @throws Throwable
     */
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

    /**
     * @throws CommonMarkException
     */
    private function sendMarkdownMessage(Nutgram $bot, string $message): void
    {
        $this->markdownSender->send(bot: $bot, text: $message);
    }

    private function sendTyping(Nutgram $bot): void
    {
        $bot->sendChatAction(ChatAction::TYPING);
    }

    private function checkEndMessage(string $message): bool
    {
        return trim(mb_strtolower($message)) === mb_strtolower(AiTalkCommand::STOP_TALK->value);
    }
}