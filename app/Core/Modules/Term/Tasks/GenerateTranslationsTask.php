<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Ai\Factories\AiAgentFactory;
use App\Core\Modules\Term\Dto\StoreTranslationTermDto;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\Term\Mappers\StoreTranslationMapper;
use App\Core\Modules\Term\Models\TermVariant;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Throwable;

final class GenerateTranslationsTask extends AiAgentTermProcessingTask
{
    private const int CHUNK_SIZE              = 500;
    private const int MAX_TERM_VARIANTS_COUNT = 10;

    public function __construct(
        private readonly AiAgentFactory $agentFactory,
        private readonly StoreTranslationMapper $mapper,
    ){}

    /**
     * @param bool $isOnlyEmpty
     * @return iterable<StoreTranslationTermDto>
     * @throws BindingResolutionException
     * @throws AiUnavailableException
     */
    public function run(bool $isOnlyEmpty = false): iterable
    {
        $agent = $this->agentFactory->make(AiAgentType::TERM_TRANSLATOR);
        $generator = $this->getTermVariantsGenerator($isOnlyEmpty);

        foreach (chunk_iterable($generator, self::MAX_TERM_VARIANTS_COUNT) as $variantsChunk) {
            try {
                $translatedTerms = $this->processChunk($agent, $variantsChunk);
                if ($translatedTerms === null) {
                    continue;
                }

                foreach ($translatedTerms as $translatedTerm) {
                    yield $this->mapper->mapRawToDto($translatedTerm);
                }

            } catch (AiUnavailableException $e) {
                throw $e;
            } catch (Throwable $e) {
                Log::warning('Generate term translations failed.',
                    ['exception' => $e->getMessage()]
                );
            }
        }
    }

    /**
     * @return LazyCollection<int, TermVariant>
     */
    private function getTermVariantsGenerator(bool $isOnlyEmpty): LazyCollection
    {
        $query = TermVariant::query()
            ->select(['id', 'term_id', 'pos'])
            ->with(['term:id,text,type']);

        if ($isOnlyEmpty) {
            $query->doesntHave('translations');
        }

        return $query->lazyById(self::CHUNK_SIZE);
    }

    /**
     * @param TermVariant[] $items
     * @return string|false
     */
    protected function serializeItems(array $items): string|false
    {
        $terms = [];
        foreach ($items as $variant) {
            $term = $variant->term;

            $item = [
                'term' => $term->text,
                'type' => $term->type->value,
            ];
            if ($term->type === TermType::WORD) {
                $item['pos'] = $variant->pos->value;
            }

            $terms[] = $item;
        }

        return json_encode($terms, JSON_UNESCAPED_UNICODE);
    }
}