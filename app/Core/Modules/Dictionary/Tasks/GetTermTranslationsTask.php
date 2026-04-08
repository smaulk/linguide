<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Dto\TermTranslationDatasetDto;
use App\Core\Modules\Dictionary\Mappers\TranslationsDatasetMapper;
use App\Core\Modules\Dictionary\Models\TermVariant;
use Illuminate\Support\LazyCollection;

final class GetTermTranslationsTask extends Task
{
    private const int CHUNK_SIZE = 500;

    public function __construct(private readonly TranslationsDatasetMapper $mapper){}

    /**
     * @return iterable<TermTranslationDatasetDto[]>
     */
    public function run(): iterable
    {
        $generator = $this->getTermTranslationsGenerator();

        foreach (chunk_iterable($generator, self::CHUNK_SIZE) as $terms) {
            yield $this->mapper->mapModelArrayToDtoArray($terms);
        }
    }

    /**
     * @return LazyCollection<int, TermVariant>
     */
    private function getTermTranslationsGenerator(): LazyCollection
    {
        return TermVariant::query()
            ->select(['id', 'term_id', 'pos'])
            ->with([
                'term:id,text,type',
                'translations',
                'translations.examples'
            ])
            ->lazyById(self::CHUNK_SIZE);
    }
}