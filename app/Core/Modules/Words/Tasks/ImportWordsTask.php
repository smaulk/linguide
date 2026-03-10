<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Words\Dto\WordImportDto;
use App\Core\Modules\Words\Models\Word;

final class ImportWordsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<WordImportDto> $dtoWords
     * @return int количество импортированных слов
     */
    public function run(iterable $dtoWords): int
    {
        $buffer = [];
        $imported = 0;

        foreach ($dtoWords as $dto) {
            $buffer[] = $this->prepareWordRow($dto);

            if (count($buffer) >= self::BATCH_SIZE) {
                $imported += $this->insertBatch($buffer);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            $imported += $this->insertBatch($buffer);
        }

        return $imported;
    }

    /**
     * @return array<string,mixed>
     */
    private function prepareWordRow(WordImportDto $dto): array
    {
        return [
            'text'       => $dto->text,
            'pos'        => $dto->pos->value,
            'level'      => $dto->level->value,
            'created_at' => now(),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $buffer
     */
    private function insertBatch(array $buffer): int
    {
        return Word::query()->insertOrIgnore($buffer);
    }
}