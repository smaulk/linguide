<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Dto\WordDto;
use App\Core\Modules\Dictionary\Enums\WordReviewAnswerResult;
use LogicException;
use RuntimeException;

final class CheckWordReviewAnswerAction extends Action
{
    /**
     * @throws LogicException
     * @throws RuntimeException
     */
    public function run(WordDto $word, string $answer): WordReviewAnswerResult
    {
        if ($word->translations === []) {
            throw new LogicException("Слово {$word->id} ({$word->text}) не имеет переводов");
        }

        foreach ($word->translations as $translation) {
            if ($this->isAnswerCorrect($translation->text, $answer)) {
                return WordReviewAnswerResult::CORRECT;
            }
        }

        return WordReviewAnswerResult::WRONG;
    }

    /**
     * @throws RuntimeException
     */
    private function isAnswerCorrect(string $translation, string $answer): bool
    {
        $translation = mb_strtolower(trim($translation));
        $answer = mb_strtolower(trim($answer));

        if ($translation === $answer) {
            return true;
        }

        $valueLen = mb_strlen($translation);
        $answerLen = mb_strlen($answer);

        // Слишком большая разница в длине — сразу false
        if (abs($valueLen - $answerLen) > 2) {
            return false;
        }

        $distance = $this->levenshtein($translation, $answer);

        // Допустимое количество ошибок в зависимости от длины слова
        $tolerance = match (true) {
            $valueLen <= 4  => 0, // короткие слова — только точное совпадение
            $valueLen <= 7  => 1, // до 7 символов — 1 ошибка
            $valueLen <= 11 => 2, // до 11 символов — 2 ошибки
            default         => 3, // длинные слова — до 3 ошибок
        };

        return $distance <= $tolerance;
    }

    /**
     * Levenshtein с поддержкой многобайтовых строк (кириллица и т.д.)
     * @throws RuntimeException
     */
    private function levenshtein(string $s1, string $s2): int
    {
        // Разбиваем строки на массивы символов (mb-safe)
        $s1Chars = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
        $s2Chars = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);

        if (!$s1Chars || !$s2Chars) {
            throw new RuntimeException('Regex split failed');
        }

        $len1 = count($s1Chars);
        $len2 = count($s2Chars);

        // Инициализируем матрицу
        $dp = [];
        for ($i = 0; $i <= $len1; $i++) {
            $dp[$i][0] = $i;
        }
        for ($j = 0; $j <= $len2; $j++) {
            $dp[0][$j] = $j;
        }

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = $s1Chars[$i - 1] === $s2Chars[$j - 1] ? 0 : 1;

                $dp[$i][$j] = min(
                    $dp[$i - 1][$j] + 1,
                    $dp[$i][$j - 1] + 1,
                    $dp[$i - 1][$j - 1] + $cost
                );
            }
        }

        return $dp[$len1][$len2];
    }
}