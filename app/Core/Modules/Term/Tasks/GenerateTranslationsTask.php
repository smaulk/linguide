<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\Ai\Factories\AiAgentFactory;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use App\Core\Modules\Term\Dto\TermTranslationDatasetDto;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\Term\Mappers\TranslationsDatasetMapper;
use App\Core\Modules\Term\Models\TermVariant;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use JsonException;
use Throwable;

final class GenerateTranslationsTask extends Task
{
    private const int CHUNK_SIZE          = 500;
    private const int TERM_VARIANTS_COUNT = 10;

    public function __construct(
        private readonly AiAgentFactory $agentFactory,
        private readonly TranslationsDatasetMapper $mapper,
    ){}

    /**
     * @param bool $isOnlyEmpty
     * @return iterable<TermTranslationDatasetDto[]>
     * @throws BindingResolutionException
     */
    public function run(bool $isOnlyEmpty = false): iterable
    {
        $agent = $this->getTranslatorAgent();
        $generator = $this->getTermsGenerator($isOnlyEmpty);

        foreach (chunk_iterable($generator, self::TERM_VARIANTS_COUNT) as $variants) {
            try {
                $translatedTerms = $this->generateTranslations($agent, $variants);
                if ($translatedTerms === null) {
                    continue;
                }

                yield $this->mapper->mapRawArrayToDtoArray($translatedTerms);
            } catch (RequestException $e) {
                Log::warning('Generate term translations request failed.',
                    ['exception' => $e->getMessage()]
                );
                return;
            } catch (Throwable $e) {
                Log::warning('Generate term translations failed.', ['exception' => $e->getMessage()]);
            }
        }
    }

    /**
     * @return LazyCollection<int, TermVariant>
     */
    private function getTermsGenerator(bool $isOnlyEmpty): LazyCollection
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
     * @throws BindingResolutionException
     */
    private function getTranslatorAgent(): AiAgentContract
    {
        return $this->agentFactory->make(AiAgentType::TERM_TRANSLATOR);
    }

    /**
     * @param AiAgentContract $agent
     * @param TermVariant[] $variants
     * @return array<int, array<string, mixed>>|null
     */
    private function generateTranslations(AiAgentContract $agent, array $variants): ?array
    {
        $preparedTerms = $this->prepareTermsToAgent($variants);
        if ($preparedTerms === false) {
            return null;
        }

        $response = $this->sendTermsToAgent($agent, $preparedTerms);

        try {
            return $this->parseArrayFromResponse($response);
        } catch (JsonException $e) {
            Log::warning('Generate translations json decode failed.', ['exception' => $e->getMessage()]);
            return null;
        }
    }


    private function sendTermsToAgent(AiAgentContract $agent, string $termsMessage): string
    {
        $response = $agent->send(
            new AiMessageDto(AiMessageRole::USER, $termsMessage),
        );

        return $response->text;
    }

    /**
     * @param TermVariant[] $variants
     */
    private function prepareTermsToAgent(array $variants): string|false
    {
        $terms = [];
        foreach ($variants as $variant) {
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

    /**
     * @return array<int, array<string, mixed>>|null
     * @throws JsonException
     */
    private function parseArrayFromResponse(string $response): ?array
    {
        $json = trim($response);

        if (str_starts_with($json, '```')) {
            $json = preg_replace('/^```[a-z]*\s*|\s*```$/i', '', $json) ?? $json;
        }

        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }
}