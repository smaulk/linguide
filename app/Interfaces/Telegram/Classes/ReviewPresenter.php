<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Classes;

use App\Core\Modules\Term\Dto\ReviewSessionStatisticDto;
use App\Core\Modules\Term\Dto\ReviewTermDto;
use App\Core\Modules\Term\Dto\TermVariantDto;
use App\Core\Modules\Term\Dto\LearningProgressDto;
use App\Core\Modules\Term\Dto\TranslationExampleDto;
use App\Core\Modules\Term\Enums\ReviewMode;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Interfaces\Telegram\Response\Markdown\Render\MarkdownEscaper;

final class ReviewPresenter
{
    public function term(ReviewTermDto $reviewTerm): string
    {
        $progress = $reviewTerm->learningProgress;
        $term = $progress->termVariant;

        $rows = match ($reviewTerm->mode) {
            ReviewMode::EnglishToRussian => $this->getTermRowsEnToRu($term),
            ReviewMode::RussianToEnglish => $this->getTermRowsRuToEn($term),
        };

        $rows = [
            ...$rows,
            '',
            ...$this->buildTermReviewInfo($progress),
        ];

        return implode("\n", $rows);
    }

    /**
     * @return string[]
     */
    private function getTermRowsEnToRu(TermVariantDto $term): array
    {
        $rows = [
            "__*{$term->text}*__",
        ];

        if ($term->type === TermType::WORD) {
            $rows[] = "_\({$term->pos->ru()}\)_";
        }

        return $rows;
    }

    /**
     * @return string[]
     */
    private function getTermRowsRuToEn(TermVariantDto $term): array
    {
        $translations = [];
        foreach ($term->translations as $translation) {
            $translations[] = "__*{$translation->text}*__";
        }

        $rows[] = implode(", ", $translations);

        if ($term->type === TermType::WORD) {
            $rows[] = "_\({$term->pos->ru()}\)_";
        }

        return $rows;
    }

    /**
     * @return string[]
     */
    private function buildTermReviewInfo(LearningProgressDto $learningProgress): array
    {
        if ($learningProgress->lastReviewedAt === null) {
            return ['Новое\\!'];
        }

        $lastReview = MarkdownEscaper::escape(
            $learningProgress->lastReviewedAt->format('d.m.Y H:i')
        );

        return [
            "Повторяли: " . $lastReview,
            "Серия подряд: " . $learningProgress->repetitions,
        ];
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
            $translations[] = "{$num}. {$translation->text}  ({$translation->contextRu})";

            $example = $translation->examples[0] ?? null;
            if ($example !== null) {
                $examples[] = [$num, $example];
            }
        }

        $lines[] = "Перевод:\n" . implode("\n", $translations);
        $lines[] = 'Примеры:';

        /** @var TranslationExampleDto $example */
        foreach ($examples as [$num, $example]) {
            $lines[] = "{$num}: {$example->sentenceEn} ({$example->sentenceRu})";
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