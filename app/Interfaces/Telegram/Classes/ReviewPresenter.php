<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Classes;

use App\Core\Modules\Term\Dto\ReviewSessionStatisticDto;
use App\Core\Modules\Term\Dto\TermVariantDto;
use App\Core\Modules\Term\Dto\LearningProgressDto;
use App\Core\Modules\Term\Enums\TermType;
use App\Interfaces\Telegram\Response\Markdown\Render\MarkdownEscaper;

final class ReviewPresenter
{
    public function term(LearningProgressDto $learningProgress): string
    {
        $termVariant = $learningProgress->termVariant;
        $lastReview = MarkdownEscaper::escape(
            $learningProgress->last_reviewed_at?->format('d.m.Y H:i') ?? 'новое'
        );

        $posText = $termVariant->type === TermType::WORD ? "\n_\({$termVariant->pos->ru()}\)_" : '';

        return <<<TEXT
__*{$termVariant->text}*__$posText
 
Повторяли: {$lastReview}
Серия подряд: {$learningProgress->repetitions}
TEXT;
    }

    public function answer(TermVariantDto $termVariant, bool $correct): string
    {
        $termText = match ($termVariant->type) {
            TermType::WORD   => "Слово: {$termVariant->text} ({$termVariant->pos->ru()})",
            TermType::PHRASE => "Фраза: {$termVariant->text}"
        };

        $lines = [
            $correct ? "Верно! ✅\n" : "Неверно ❌\n",
            $termText,
        ];

        $translations = [];
        $examples = [];

        foreach (array_values($termVariant->translations) as $index => $translation) {
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
Количество: {$statistics->termsCount}
Верных ответов: {$statistics->correctTermsCount}
Среднее время ответа: {$statistics->avgResponseTime}
Максимальное время ответа: {$statistics->maxResponseTime}
Минимальное время ответа: {$statistics->minResponseTime}
TEXT;
    }
}