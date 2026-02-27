<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown;

use App\Interfaces\Telegram\Response\Markdown\Render\MarkdownConverter;
use League\CommonMark\Exception\CommonMarkException;
use RuntimeException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ForceReply;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;
use SergiX44\Nutgram\Telegram\Types\Message\Message;
use Throwable;

final readonly class MarkdownSender
{
    public function __construct(
        private MarkdownConverter $converter,
        private MarkdownSplitter $splitter,
    ){}

    /**
     * @return list<Message|null>
     * @throws CommonMarkException
     */
    public function send(
        Nutgram $bot,
        string $text,
        int|string|null $chat_id = null,
        ?int $reply_to_message_id = null,
        InlineKeyboardMarkup|ReplyKeyboardMarkup|ReplyKeyboardRemove|ForceReply|null $reply_markup = null,
    ): array
    {
        $formattedText = $this->converter->convert($text);
        $chunks = $this->splitter->split($formattedText);
        $lastIndex = count($chunks) - 1;

        return array_map(fn($chunk, $i) => $bot->sendMessage(
            text: $chunk,
            chat_id: $chat_id,
            parse_mode: ParseMode::MARKDOWN,
            reply_to_message_id: $i === 0 ? $reply_to_message_id : null,
            reply_markup: $i === $lastIndex ? $reply_markup : null,
        ), $chunks, array_keys($chunks));
    }
}