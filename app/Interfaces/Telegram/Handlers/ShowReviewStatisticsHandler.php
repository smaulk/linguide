<?php

declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\Term\Actions\GetUserReviewPeriodStatisticsAction;
use App\Core\Modules\Term\Dto\ReviewPeriodStatisticsDto;
use App\Core\Modules\Term\Enums\ReviewStatisticsPeriod;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowReviewStatisticsHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly GetUserReviewPeriodStatisticsAction $statisticsAction,
    ) {}

    public function __invoke(Nutgram $bot): void
    {
        $period = ReviewStatisticsPeriod::fromLabel(trim($bot->message()->text ?? ''));
        if ($period === null) {
            $bot->sendMessage('Не удалось определить период статистики. Выберите период ещё раз.');

            return;
        }

        $user = $this->userContext->get($bot);
        $statistics = $this->statisticsAction->run(
            $user->id,
            $period,
            $user->settings->utcOffset?->value(),
        );

        $bot->sendMessage($this->format($period, $statistics));
    }

    private function format(ReviewStatisticsPeriod $period, ReviewPeriodStatisticsDto $statistics): string
    {
        $lines = ['📊 Статистика '.$period->periodName()];
        if ($statistics->sessionsCount === 0) {
            $lines[] = 'За выбранный период повторений не было.';
        }

        $lines = array_merge($lines, [
            '',
            $this->countLabel($statistics->sessionsCount, 'сессия', 'сессии', 'сессий').' повторения',
            'Повторений терминов: '.$statistics->answeredTermsCount,
            'Верных ответов: '.number_format($statistics->correctAnswersPercent, 1, ',', '').'%',
            $this->countLabel($statistics->activeDaysCount, 'день с повторениями', 'дня с повторениями', 'дней с повторениями'),
            '',
            '📚 За всё время:',
            $this->countLabel($statistics->wellLearnedWordsCount, 'хорошо выученное слово', 'хорошо выученных слова', 'хорошо выученных слов'),
            'Самая длинная серия подряд: '.$this->countLabel($statistics->longestActivityStreak, 'день', 'дня', 'дней'),
        ]);

        return implode("\n", $lines);
    }

    private function countLabel(int $count, string $one, string $few, string $many): string
    {
        $mod100 = $count % 100;
        $mod10 = $count % 10;
        $word = match (true) {
            $mod100 >= 11 && $mod100 <= 14 => $many,
            $mod10 === 1 => $one,
            $mod10 >= 2 && $mod10 <= 4 => $few,
            default => $many,
        };

        return "$count $word";
    }
}
