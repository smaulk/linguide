<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Dto\WordDatasetDto;
use App\Core\Modules\Dictionary\Mappers\WordTranslationsDatasetMapper;
use App\Core\Modules\Dictionary\Models\Word;
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