<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Conversations;

use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Term\Actions\CheckReviewAnswerAction;
use App\Core\Modules\Term\Actions\EvaluateReviewAnswerAction;
use App\Core\Modules\Term\Actions\GetTermFromReviewSessionAction;
use App\Core\Modules\Term\Actions\GetFinishedReviewSessionStatisticAction;
use App\Core\Modules\Term\Actions\StartReviewSessionAction;
use App\Core\Modules\Term\Dto\LearningProgressDto;
use App\Core\Modules\Term\Enums\ReviewAnswerResult;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Classes\CallbackParser;
use App\Interfaces\Telegram\Classes\ReviewPresenter;
use App\Interfaces\Telegram\Commands\ReviewCommand;
use App\Interfaces\Telegram\Keyboards\Inline\ReviewInlineKeyboard;
use App\Interfaces\Telegram\Parents\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

final class ReviewConversation extends Conversation
{
    public ?int $sessionId;

    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly StartReviewSessionAction $startSessionAction,
        private readonly GetTermFromReviewSessionAction $getTermAction,
        private readonly CheckReviewAnswerAction $checkAnswerAction,
        private readonly EvaluateReviewAnswerAction $evaluateAnswerAction,
        private readonly GetFinishedReviewSessionStatisticAction $getStatisticsAction,
        private readonly ReviewInlineKeyboard $inlineKeyboard,
        private readonly ReviewPresenter $presenter,
    ){}

    public function start(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);

        $sessionId = $this->startSession($appUser->id);
        if ($sessionId === null) {
            $bot->sendMessage('Нет возможности запустить повторение :(');
            $this->end();
            return;
        }

        $this->sendNextTermOrEnd($bot, $sessionId, $appUser->settings->utcOffset);
    }

    public function checkTerm(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);
        $utcOffset = $appUser->settings->utcOffset;

        $sessionId = $this->getSessionId();
        if ($sessionId === null) {
            $bot->sendMessage('Произошла неожиданная ошибка.');
            $this->end();
            return;
        }

        $learningProgress = $this->getLearningProgress($sessionId, $utcOffset);
        if ($learningProgress === null) {
            $this->endReview($bot, $sessionId);
            return;
        }

        $answerResult = $this->resolveAnswerResult($bot, $learningProgress);
        if ($answerResult === null) {
            return;
        }

        $this->processAnswer($bot, $sessionId, $learningProgress, $answerResult);
        $this->sendNextTermOrEnd($bot, $sessionId, $utcOffset);
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

    private function resolveAnswerResult(Nutgram $bot, LearningProgressDto $progress): ?ReviewAnswerResult
    {
        if ($this->isForgotCallback($bot)) {
            $bot->editMessageReplyMarkup();
            return ReviewAnswerResult::WRONG;
        }

        $message = $this->getMessageText($bot);

        return $message !== null
            ? $this->checkAnswerAction->run($progress->termVariant, $message)
            : null;
    }

    private function sendNextTermOrEnd(Nutgram $bot, int $sessionId, ?UtcOffset $utcOffset): void
    {
        $next = $this->getLearningProgress($sessionId, $utcOffset);
        if ($next === null) {
            $this->endReview($bot, $sessionId);
            return;
        }

        $this->sendTerm($bot, $next);
        $this->next('checkTerm');
    }

    private function getLearningProgress(int $sessionId, ?UtcOffset $utcOffset): ?LearningProgressDto
    {
        return $this->getTermAction->run($sessionId, $utcOffset);
    }

    private function endReview(Nutgram $bot, int $sessionId): void
    {
        $statistics = $this->getStatisticsAction->run($sessionId);
        $bot->sendMessage($this->presenter->statistics($statistics));
        $this->end();
    }

    private function processAnswer(
        Nutgram $bot, int $sessionId, LearningProgressDto $progress, ReviewAnswerResult $answerResult
    ): void
    {
        $this->evaluateAnswerAction->run($sessionId, $progress->termVariant->id, $answerResult);

        $bot->sendMessage(
            $this->presenter->answer(
                termVariant: $progress->termVariant,
                correct: $answerResult === ReviewAnswerResult::CORRECT
            ),
        );
    }

    private function sendTerm(Nutgram $bot, LearningProgressDto $progress): void
    {
        $bot->sendMessage(
            text: $this->presenter->term($progress),
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

        return CallbackParser::isMatch($callback, ReviewCommand::FORGOT->value);
    }

    private function getMessageText(Nutgram $bot): ?string
    {
        $text = trim($bot->message()->text ?? '');
        return $text !== '' ? $text : null;
    }
}