<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Classes;

use App\Core\Modules\Dictionary\Dto\ReviewSessionStatisticDto;
use App\Core\Modules\Dictionary\Dto\WordDto;
use App\Core\Modules\Dictionary\Dto\WordProgressDto;
use App\Interfaces\Telegram\Response\Markdown\Render\MarkdownEscaper;

final class ReviewWordPresenter
{
    public function wordCard(WordProgressDto $wordProgress): string
    {
        $word = $wordProgress->word;
        $lastReview = MarkdownEscaper::escape(
            $wordProgress->last_reviewed_at?->format('d.m.Y H:i') ?? '—'
        );

        return <<<TEXT
__*{$word->text}*__
_\({$word->pos->ru()}\)_
 
Повторяли: {$lastReview}
Серия подряд: {$wordProgress->repetitions}
TEXT;
    }

    public function wordAnswer(WordDto $word, bool $correct): string
    {
        $lines = [];

        if ($correct) {
            $lines[] = "Верно! ✅\n";
        }

        $lines[] = "Слово: {$word->text} ({$word->pos->ru()})";

        $translations = [];
        $examples = [];

        foreach (array_values($word->translations) as $index => $translation) {
            $num = $index + 1;
            $translations[] = "{$num}. {$translation->text}  ({$translation->context_ru})";

            $example = $translation->examples[0] ?? null;
            if ($example !== null) {
                $examples[] = [$num, $example];
            }
        }

        $lines[] = "Перевод:\n" . implode("\n", $translations);
        $lines[] = 'Примеры:';

        foreach ($examples as [$num, $example]) {
            $lines[] = "{$num}: {$example->sentence_en} ({$example->sentence_ru})";
        }

        return implode("\n", $lines);
    }

    public function statistics(ReviewSessionStatisticDto $statistics): string
    {
        return <<<TEXT
Повторение закончено!
 
📊Статистика
Длительность: {$statistics->duration}
Количество слов: {$statistics->wordsCount}
Верных ответов: {$statistics->correctWordsCount}
Среднее время ответа: {$statistics->avgResponseTime}
Максимальное время ответа: {$statistics->maxResponseTime}
Минимальное время ответа: {$statistics->minResponseTime}
TEXT;
    }
}