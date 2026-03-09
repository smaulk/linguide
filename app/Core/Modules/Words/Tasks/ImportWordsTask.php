<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Words\Dto\RawWordDto;
use App\Core\Modules\Words\Enums\PartOfSpeechType;
use App\Core\Modules\Words\Models\Word;

final class ImportWordsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<RawWordDto> $rawWords
     * @return int количество импортированных слов
     */
    public function run(iterable $rawWords): int
    {
        $buffer = [];
        $imported = 0;

        foreach ($rawWords as $dto) {
            $row = $this->normalizeWord($dto);
            if ($row === null) {
                continue;
            }

            $buffer[] = $row;

            if (count($buffer) >= self::BATCH_SIZE) {
                $imported += $this->flushBatch($buffer);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            $imported += $this->flushBatch($buffer);
        }

        return $imported;
    }

    /**
     * Преобразует DTO в строку для insert.
     *
     * @return array<string,mixed>|null
     */
    private function normalizeWord(RawWordDto $dto): ?array
    {
        $text = trim($dto->text);
        $pos = trim($dto->pos);
        $level = trim($dto->level);

        if ($text === '' || $pos === '' || $level === '') {
            return null;
        }

        $pos = PartOfSpeechType::tryFrom(strtolower($pos));
        $level = LanguageLevel::tryFrom(strtoupper($level));

        if ($pos === null || $level === null) {
            return null;
        }

        return [
            'text'       => strtolower($text),
            'pos'        => $pos->value,
            'level'      => $level->value,
            'created_at' => now(),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $buffer
     */
    private function flushBatch(array $buffer): int
    {
        return Word::query()->insertOrIgnore($buffer);
    }
}