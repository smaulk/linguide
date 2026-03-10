<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Words\Dto\WordDatasetDto;
use App\Core\Modules\Words\Mappers\WordTranslationsDatasetMapper;
use App\Core\Modules\Words\Models\Word;
use Illuminate\Support\LazyCollection;

final class GetWordTranslationsTask extends Task
{
    private const int CHUNK_SIZE = 500;

    public function __construct(private readonly WordTranslationsDatasetMapper $mapper){}

    /**
     * @return iterable<WordDatasetDto[]>
     */
    public function run(): iterable
    {
        $generator = $this->getWordsTranslationsGenerator();

        foreach (chunk_iterable($generator, self::CHUNK_SIZE) as $words) {
            yield $this->mapper->mapModelArrayToDtoArray($words);
        }
    }

    /**
     * @return LazyCollection<int, Word>
     */
    private function getWordsTranslationsGenerator(): LazyCollection
    {
        return Word::query()
            ->select(['id', 'text', 'pos'])
            ->with([
                'translations',
                'translations.examples'
            ])
            ->lazyById(self::CHUNK_SIZE);
    }
}