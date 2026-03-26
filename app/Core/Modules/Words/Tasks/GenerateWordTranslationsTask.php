<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\Ai\Factories\AiAgentFactory;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use App\Core\Modules\Words\Dto\WordDatasetDto;
use App\Core\Modules\Words\Mappers\WordTranslationsDatasetMapper;
use App\Core\Modules\Words\Models\Word;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use JsonException;
use Throwable;

final class GenerateWordTranslationsTask extends Task
{
    private const int CHUNK_SIZE  = 500;
    private const int WORDS_COUNT = 10;

    public function __construct(
        private readonly AiAgentFactory $agentFactory,
        private readonly WordTranslationsDatasetMapper $mapper,
    ){}

    /**
     * @param bool $isOnlyEmpty
     * @return iterable<WordDatasetDto[]>
     * @throws BindingResolutionException
     */
    public function run(bool $isOnlyEmpty = false): iterable
    {
        $agent = $this->getTranslatorAgent();
        $generator = $this->getWordsGenerator($isOnlyEmpty);

        foreach (chunk_iterable($generator, self::WORDS_COUNT) as $words) {
            try {
                $translations = $this->generateTranslations($agent, $words);
                if ($translations === null) {
                    continue;
                }

                yield $this->mapper->mapRawArrayToDtoArray($translations);
            } catch (Throwable $e) {
                Log::warning('Generate word translations failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * @return LazyCollection<int, Word>
     */
    private function getWordsGenerator(bool $isOnlyEmpty): LazyCollection
    {
        $query = Word::query()
            ->select(['id', 'text', 'pos']);

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
        return $this->agentFactory->make(AiAgentType::WORD_TRANSLATOR);
    }

    /**
     * @param AiAgentContract $agent
     * @param Word[] $words
     * @return array<int, array<string, mixed>>|null
     */
    private function generateTranslations(AiAgentContract $agent, array $words): ?array
    {
        $preparedWords = $this->prepareWordsToAgent($words);
        if ($preparedWords === false) {
            return null;
        }

        $response = $this->sendWordsToAgent($agent, $preparedWords);

        try {
            return $this->parseArrayFromResponse($response);
        } catch (JsonException $e) {
            Log::warning('Generate word translations json decode failed: ' . $e->getMessage());
            return null;
        }
    }


    private function sendWordsToAgent(AiAgentContract $agent, string $wordsMessage): string
    {
        $response = $agent->send(
            new AiMessageDto(AiMessageRole::USER, $wordsMessage),
        );

        return $response->text;
    }

    /**
     * @param Word[] $words
     */
    private function prepareWordsToAgent(array $words): string|false
    {
        $wordsData = array_map(fn(Word $word) => [
            'word' => $word->text,
            'pos'  => $word->pos->value,
        ], $words);

        return json_encode($wordsData, JSON_UNESCAPED_UNICODE);
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