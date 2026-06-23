<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Ai\Factories\AiAgentFactory;
use App\Core\Modules\Term\Dto\ValidatedCandidateDto;
use App\Core\Modules\Term\Mappers\ValidatedCandidateMapper;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;

final class ValidateTermCandidatesTask extends AiAgentTermProcessingTask
{
    private const int MAX_CANDIDATES_COUNT = 10;

    public function __construct(
        private readonly AiAgentFactory $agentFactory,
        private readonly ValidatedCandidateMapper $mapper,
    ){}

    /**
     * @param iterable<string> $candidates
     * @return iterable<ValidatedCandidateDto[]>
     * @throws BindingResolutionException
     * @throws AiUnavailableException
     * @throws Throwable
     */
    public function run(iterable $candidates): iterable
    {
        $agent = $this->agentFactory->make(AiAgentType::CANDIDATE_VALIDATOR);;

        foreach (chunk_iterable($candidates, self::MAX_CANDIDATES_COUNT) as $termsChunk) {
            $definedTerms = $this->processChunk($agent, $termsChunk);
            if ($definedTerms === null) {
                continue;
            }

            yield $this->mapper->mapRawArrayToDtoArray($definedTerms);
        }
    }

    /**
     * @param string[] $items
     * @return string|false
     */
    protected function serializeItems(array $items): string|false
    {
        return json_encode($items, JSON_UNESCAPED_UNICODE);
    }
}