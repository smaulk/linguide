<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Normalizers;

final class TermNormalizer
{
    /**
     * @param string[] $terms
     * @return string[]
     */
    public function normalize(array $terms): array
    {
        if (empty($terms)) {
            return [];
        }

        return collect($terms)
            ->map(function (string $term) {
                // нижний регистр
                $term = mb_strtolower($term);
                // апострофы (` → ')
                $term = str_replace('`', "'", $term);
                // тире → дефис
                $term = preg_replace('/[–—]/u', '-', $term) ?? '';
                // схлопываем пробелы
                $term = preg_replace('/\s+/', ' ', $term) ?? '';
                // обрезаем по краям
                return trim($term);
            })
            ->filter()      // убираем пустые строки
            ->unique()      // убираем дубликаты
            ->values()
            ->all();
    }
}