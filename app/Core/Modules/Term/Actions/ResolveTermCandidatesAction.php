<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Term\Dto\StoreTermDto;
use App\Core\Modules\Term\Dto\UpdateCandidateDto;
use App\Core\Modules\Term\Dto\ValidatedCandidateDto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Enums\TermCandidateStatus;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\Term\Models\Term;
use App\Core\Modules\Term\Models\TermCandidate;
use App\Core\Modules\Term\Tasks\StoreTermsTask;
use App\Core\Modules\Term\Tasks\UpdateTermCandidatesTask;
use App\Core\Modules\Term\Tasks\ValidateTermCandidatesTask;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

/**
 * Обрабатывает кандидаты в термины со статусом Pending
 */
final class ResolveTermCandidatesAction extends Action
{
    public function __construct(
        private readonly ValidateTermCandidatesTask $validateCandidatesTask,
        private readonly StoreTermsTask $storeTermsTask,
        private readonly UpdateTermCandidatesTask $updateCandidatesTask,
    ){}

    /**
     * @throws Throwable
     * @throws BindingResolutionException
     * @throws AiUnavailableException
     */
    public function run(): void
    {
        TermCandidate::query()
            ->where('status', TermCandidateStatus::PENDING)
            ->orWhere(function ($query) {
                $query->where('status', TermCandidateStatus::VALID)
                    ->whereNull('term_id');
            })
            ->chunkById(100, fn(Collection $candidates) => $this->process($candidates));
    }

    /**
     * @param Collection<int, TermCandidate> $candidates
     * @throws BindingResolutionException
     * @throws Throwable
     * @throws AiUnavailableException
     */
    private function process(Collection $candidates): void
    {
        /** @var array<string, int> $candidatesMap */
        $candidatesMap = $candidates->pluck('id', 'raw_term')->all();
        $candidatesRawTerm = array_keys($candidatesMap);

        $existingTerms = $this->getExistingTerms($candidatesRawTerm);
        $updateCandidates = $this->buildUpdateFromExisting($candidatesMap, $existingTerms);

        $termsToValidate = array_diff($candidatesRawTerm, array_keys($existingTerms));
        if (!empty($termsToValidate)) {
            $validatedCandidates = $this->validateCandidates($termsToValidate);
            $newTerms = $this->storeTermsFromCandidates($validatedCandidates);

            $updateCandidates = [
                ...$updateCandidates,
                ...$this->buildUpdateFromValidated($candidatesMap, $validatedCandidates, $newTerms),
            ];
        }

        $this->updateCandidatesTask->run($updateCandidates);
    }

    /**
     * @param array<string, int> $candidatesMap
     * @param array<string, int> $existingTerms
     * @return UpdateCandidateDto[]
     */
    private function buildUpdateFromExisting(array $candidatesMap, array $existingTerms): array
    {
        $result = [];
        foreach ($existingTerms as $termText => $termId) {
            $candidateId = $candidatesMap[$termText] ?? null;
            if ($candidateId === null) {
                continue;
            }

            $result[] = new UpdateCandidateDto(
                candidateId: $candidateId,
                isValid: true,
                termId: $termId,
            );
        }

        return $result;
    }

    /**
     * @param array<string, int> $candidatesMap
     * @param ValidatedCandidateDto[] $validatedCandidates
     * @param array<string, int> $newTerms
     * @return UpdateCandidateDto[]
     *
     */
    private function buildUpdateFromValidated(array $candidatesMap, array $validatedCandidates, array $newTerms): array
    {
        $result = [];
        foreach ($validatedCandidates as $candidate) {
            $candidateId = $candidatesMap[$candidate->term] ?? null;
            if ($candidateId === null) {
                continue;
            }

            $termId = $candidate->baseForm !== null
                ? $newTerms[$candidate->baseForm] ?? null
                : null;

            $result[] = new UpdateCandidateDto(
                candidateId: $candidateId,
                isValid: $candidate->isValid,
                termId: $termId,
            );
        }

        return $result;
    }

    /**
     * Получаем существующие термины для данных кандидатов
     * @param string[] $candidates
     * @return array<string, int>
     */
    private function getExistingTerms(array $candidates): array
    {
        return Term::query()
            ->whereIn('text', $candidates)
            ->pluck('id', 'text')
            ->all();
    }

    /**
     * Запускаем ИИ валидацию кандидатов
     *
     * @param string[] $candidates
     * @return ValidatedCandidateDto[]
     * @throws BindingResolutionException
     * @throws AiUnavailableException
     * @throws Throwable
     */
    private function validateCandidates(array $candidates): array
    {
        $validatedTerms = [];
        foreach ($this->validateCandidatesTask->run($candidates) as $chunk) {
            array_push($validatedTerms, ...$chunk);
        }

        return $validatedTerms;
    }

    /**
     * Создаем термины для валидных кандидатов
     * @param ValidatedCandidateDto[] $validatedCandidates
     * @return array<string, int>
     * @throws Throwable
     */
    private function storeTermsFromCandidates(array $validatedCandidates): array
    {
        $terms = $this->buildTermsFromCandidates($validatedCandidates);
        if (empty($terms)) {
            return [];
        }

        $result = $this->storeTermsTask->run($terms);

        return $result->termIdsByText;
    }

    /**
     * @param ValidatedCandidateDto[] $validatedCandidates
     * @return StoreTermDto[]
     */
    private function buildTermsFromCandidates(array $validatedCandidates): array
    {
        $result = [];
        foreach ($validatedCandidates as $candidate) {
            if (!$candidate->isValid || $candidate->baseForm === null || $candidate->type === null) {
                continue;
            }

            if ($candidate->type === TermType::PHRASE) {
                $result[] = new StoreTermDto(
                    text: $candidate->baseForm,
                    type: TermType::PHRASE,
                    pos: PartOfSpeech::UNKNOWN,
                    isVerified: false,
                );
                continue;
            }

            if ($candidate->pos === null) {
                continue;
            }

            foreach ($candidate->pos as $pos) {
                $result[] = new StoreTermDto(
                    text: $candidate->baseForm,
                    type: $candidate->type,
                    pos: $pos,
                    isVerified: false,
                );
            }
        }

        return $result;
    }
}