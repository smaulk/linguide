<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Conversations;

use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Dictionary\Actions\CheckWordReviewAnswerAction;
use App\Core\Modules\Dictionary\Actions\EvaluateWordReviewAnswerAction;
use App\Core\Modules\Dictionary\Actions\GetWordFromReviewSessionAction;
use App\Core\Modules\Dictionary\Actions\GetFinishedReviewSessionStatisticAction;
use App\Core\Modules\Dictionary\Actions\StartWordReviewSessionAction;
use App\Core\Modules\Dictionary\Dto\WordProgressDto;
use App\Core\Modules\Dictionary\Enums\WordReviewAnswerResult;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Classes\CallbackParser;
use App\Interfaces\Telegram\Classes\ReviewWordPresenter;
use App\Interfaces\Telegram\Commands\ReviewWordsCommand;
use App\Interfaces\Telegram\Keyboards\Inline\ReviewWordInlineKeyboard;
use App\Interfaces\Telegram\Parents\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

final class ReviewWordsConversation extends Conversation
{
    public ?int $sessionId;

    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly StartWordReviewSessionAction $startSessionAction,
        private readonly GetWordFromReviewSessionAction $getWordAction,
        private readonly CheckWordReviewAnswerAction $checkAnswerAction,
        private readonly EvaluateWordReviewAnswerAction $evaluateAnswerAction,
        private readonly GetFinishedReviewSessionStatisticAction $getStatisticsAction,
        private readonly ReviewWordInlineKeyboard $inlineKeyboard,
        private readonly ReviewWordPresenter $presenter,
    ){}

    public function start(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);

        $sessionId = $this->startSession($appUser->id);
        if ($sessionId === null) {
            $bot->sendMessage('Нет возможности запустить повторение слов :(');
            $this->end();
            return;
        }

        $this->sendNextWordOrEnd($bot, $sessionId, $appUser->settings->utcOffset);
    }

    public function checkWord(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);
        $utcOffset = $appUser->settings->utcOffset;

        $sessionId = $this->getSessionId();


        $wordProgress = $this->getWordProgress($sessionId, $utcOffset);
        if ($wordProgress === null) {
            $this->endReview($bot, $sessionId);
            return;
        }

        $answerResult = $this->resolveAnswerResult($bot, $wordProgress);
        if ($answerResult === null) {
            return;
        }

        $this->processAnswer($bot, $sessionId, $wordProgress, $answerResult);
        $this->sendNextWordOrEnd($bot, $sessionId, $utcOffset);
    }

    private function getSessionId(): ?int
    {
        return $this->sessionId;
    }

    private function startSession(int $userId): ?int
    {
        $this->sessionId = $this->startSessionAction->run($userId);
        return $this->sessionId;
    }

    private function resolveAnswerResult(Nutgram $bot, WordProgressDto $wordProgress): ?WordReviewAnswerResult
    {
        if ($this->isForgotCallback($bot)) {
            $bot->editMessageReplyMarkup();
            return WordReviewAnswerResult::WRONG;
        }

        $message = $this->getMessageText($bot);

        return $message !== null
            ? $this->checkAnswerAction->run($wordProgress->word, $message)
            : null;
    }

    private function sendNextWordOrEnd(Nutgram $bot, int $sessionId, ?UtcOffset $utcOffset): void
    {
        $next = $this->getWordProgress($sessionId, $utcOffset);
        if ($next === null) {
            $this->endReview($bot, $sessionId);
            return;
        }

        $this->sendWord($bot, $next);
        $this->next('checkWord');
    }

    private function getWordProgress(int $sessionId, ?UtcOffset $utcOffset): ?WordProgressDto
    {
        return $this->getWordAction->run($sessionId, $utcOffset);
    }

    private function endReview(Nutgram $bot, int $sessionId): void
    {
        $statistics = $this->getStatisticsAction->run($sessionId);
        $bot->sendMessage($this->presenter->statistics($statistics));
        $this->end();
    }

    private function processAnswer(
        Nutgram $bot, int $sessionId, WordProgressDto $wordProgress, WordReviewAnswerResult $answerResult
    ): void
    {
        $this->evaluateAnswerAction->run($sessionId, $wordProgress->word->id, $answerResult);

        $bot->sendMessage(
            $this->presenter->wordAnswer(
                word: $wordProgress->word,
                correct: $answerResult === WordReviewAnswerResult::CORRECT
            ),
        );
    }

    private function sendWord(Nutgram $bot, WordProgressDto $wordProgress): void
    {
        $bot->sendMessage(
            text: $this->presenter->wordCard($wordProgress),
            parse_mode: ParseMode::MARKDOWN,
            reply_markup: $this->inlineKeyboard->make(),
        );
    }

    private function isForgotCallback(Nutgram $bot): bool
    {
        $callback = $bot->callbackQuery()?->data;
        if ($callback === null) {
            return false;
        }

        return CallbackParser::isMatch($callback, ReviewWordsCommand::FORGOT_WORD->value);
    }

    private function getMessageText(Nutgram $bot): ?string
    {
        $text = trim($bot->message()->text ?? '');
        return $text !== '' ? $text : null;
    }
}