<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Validators;

class TermValidator
{
    /**
     * @param string[] $terms
     * @return string[]
     */
    public function validate(array $terms): array
    {
        if (empty($terms)) {
            return [];
        }

        return collect($terms)
            ->filter(fn($term) => (bool)preg_match('/^[a-z\s\'-]+$/i', $term)) // только лат. буквы, пробелы, ' и -
            ->filter(fn($term) => mb_strlen($term) >= 2) // длина не меньше 2 символов
            ->filter(fn($term) => mb_strlen($term) <= 50) // длина не больше 50 символов
            ->values()
            ->all();
    }
}