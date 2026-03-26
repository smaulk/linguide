<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Conversations;

use App\Core\Modules\Words\Actions\EvaluateWordAnswerAction;
use App\Core\Modules\Words\Actions\GetWordsToRepeatAction;
use App\Core\Modules\Words\Dto\WordDto;
use App\Core\Modules\Words\Dto\WordProgressDto;
use App\Core\Modules\Words\Dto\WordTranslationDto;
use App\Core\Modules\Words\Enums\WordAnswerResult;
use App\Interfaces\Telegram\Keyboards\Inline\RepeatWordInlineKeyboard;
use App\Interfaces\Telegram\Parents\Conversation;
use App\Interfaces\Telegram\Response\Markdown\Render\MarkdownEscaper;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

final class RepeatWordsConversation extends Conversation
{
    /** @var WordProgressDto[] array */
    public array $words = [];

    public int $index = 0;

    public function __construct(
        private readonly GetWordsToRepeatAction $getWordsAction,
        private readonly RepeatWordInlineKeyboard $inlineKeyboard,
        private readonly EvaluateWordAnswerAction $evaluateAnswerAction,
    ){}

    private function incrementIndex(): bool
    {
        $this->index++;
        return count($this->words) > $this->index;
    }

    private function getWordProgress(): WordProgressDto
    {
        return $this->words[$this->index];
    }

    public function start(Nutgram $bot): void
    {
        $appUser = $this->getAppUser($bot);
        $this->words = $this->getWordsAction->run($appUser->id);

        if (empty($this->words)) {
            $bot->sendMessage('Для вас не нашлось слов на данный момент, повторите в другой день!');
            $this->end();
            return;
        }

        $this->sendWord($bot, $this->getWordProgress());
        $this->next('checkWord');
    }

    public function checkWord(Nutgram $bot): void
    {
        $wordProgress = $this->getWordProgress();

        if ($this->checkCallback($bot)) {
            $bot->answerCallbackQuery();
            $bot->editMessageReplyMarkup();

            $this->processWrongAnswer($bot, $wordProgress);
        } else {
            $message = $this->getMessageText($bot);
            if ($message === null) {
                return;
            }

            $this->checkCorrectAnswer($wordProgress->word, $message)
                ? $this->processCorrectAnswer($bot, $wordProgress)
                : $this->processWrongAnswer($bot, $wordProgress);
        }

        if (!$this->incrementIndex()) {
            $bot->sendMessage('Слова закончились!');
            $this->end();
            return;
        }

        $this->sendWord($bot, $this->getWordProgress());
        $this->next('checkWord');
    }

    private function processCorrectAnswer(Nutgram $bot, WordProgressDto $progress): void
    {
        $bot->sendMessage('Верно! ✅');
        $this->evaluateAnswerAction->run($progress->id, WordAnswerResult::CORRECT);
    }

    private function processWrongAnswer(Nutgram $bot, WordProgressDto $progress): void
    {
        $bot->sendMessage($this->getWordString($progress->word));
        $this->evaluateAnswerAction->run($progress->id, WordAnswerResult::WRONG);
    }

    private function checkCallback(Nutgram $bot): bool
    {
        $callback = $bot->callbackQuery()?->data;

        return $callback === 'word:forgot';
    }

    private function getMessageText(Nutgram $bot): ?string
    {
        $message = trim($bot->message()?->text ?: '');
        if ($message === '') {
            return null;
        }

        return $message;
    }

    private function sendWord(Nutgram $bot, WordProgressDto $wordProgress): void
    {
        $word = $wordProgress->word;
        $lastReview = MarkdownEscaper::escape(
            $wordProgress->last_reviewed_at?->format('d.m.Y H:i') ?? '—'
        );

        $text = <<<TEXT
__*{$word->text}*__
_\({$word->pos->ru()}\)_

Повторяли: {$lastReview}
Серия подряд: {$wordProgress->repetitions}
TEXT;

        $bot->sendMessage(
            text: $text,
            parse_mode: ParseMode::MARKDOWN,
            reply_markup: $this->inlineKeyboard->make(),
        );
    }

    private function checkCorrectAnswer(WordDto $word, string $answer): bool
    {
        foreach ($word->translations as $translation) {
            similar_text($translation->text, $answer, $percent);
            if ($percent >= 70) {
                return true;
            }
        }

        return false;
    }

    private function getPrimaryTranslation(WordDto $word): ?WordTranslationDto
    {
        return array_find(
            $word->translations,
            fn(WordTranslationDto $translation) => $translation->rank === 1
        );
    }

    private function getWordString(WordDto $word): string
    {
        $str[] = "Слово: {$word->text} ({$word->pos->ru()})";

        $translation = $this->getPrimaryTranslation($word);
        if($translation === null) {
            return 'Перевод не найден!';
        }

        $str[] = "Перевод: {$translation->text} ({$translation->context_ru})";
        $str[] = 'Примеры:';

        foreach ($translation->examples as $id => $example) {
            $index = $id + 1;
            $str[] = "{$index}: {$example->sentence_en} ({$example->sentence_ru})";
        }

        return implode("\n", $str);
    }
}