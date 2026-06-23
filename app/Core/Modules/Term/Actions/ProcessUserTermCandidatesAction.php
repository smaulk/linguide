<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Dto\StoreLearningProgressDto;
use App\Core\Modules\Term\Enums\TermCandidateStatus;
use App\Core\Modules\Term\Models\UserTermCandidate;
use App\Core\Modules\Term\Tasks\StoreLearningProgressTask;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ProcessUserTermCandidatesAction extends Action
{
    public function __construct(
        private readonly StoreLearningProgressTask $storeLearningProgressTask,
    ){}

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        UserTermCandidate::query()
            ->select(['id', 'user_id', 'candidate_id'])
            ->where('is_processed', false)
            ->with([
                'candidate:id,status,term_id',
                'candidate.term:id',
                'candidate.term.variants:id,term_id',
            ])
            ->whereHas('candidate', function ($query) {
                $query->where('status', '!=', TermCandidateStatus::PENDING);
            })
            ->chunkById(100, fn(Collection $userCandidates) => $this->process($userCandidates));
    }

    /**
     * @param Collection<int, UserTermCandidate> $userCandidates
     * @throws Throwable
     */
    private function process(Collection $userCandidates): void
    {
        $validCandidates = $this->getValidCandidates($userCandidates);

        DB::transaction(function () use ($userCandidates, $validCandidates) {
            if ($validCandidates->isNotEmpty()) {
                $this->addToLearning($validCandidates);
            }

            $this->updateUserCandidates(
                $userCandidates->pluck('id')->all(),
            );
        });
    }

    /**
     * @param Collection<int, UserTermCandidate> $userCandidates
     * @return Collection<int, UserTermCandidate>
     */
    private function getValidCandidates(Collection $userCandidates): Collection
    {
        return $userCandidates->filter(
            fn(UserTermCandidate $candidate) => $candidate->candidate->status === TermCandidateStatus::VALID
                && $candidate->candidate->term_id !== null
        );
    }

    /**
     * @param Collection<int, UserTermCandidate> $userCandidates
     */
    private function addToLearning(Collection $userCandidates): void
    {
        $items = $userCandidates->groupBy('user_id')
            ->map(fn(Collection $candidates, int $userId) => new StoreLearningProgressDto(
                userId: $userId,
                termVariantIds: $this->extractVariantIds($candidates),
            ))
            ->values()
            ->all();

        $this->storeLearningProgressTask->run($items);
    }

    /**
     * @param Collection<int, UserTermCandidate> $candidates
     * @return int[]
     */
    private function extractVariantIds(Collection $candidates): array
    {
        return $candidates
            ->flatMap(
                fn(UserTermCandidate $candidate) => $candidate->candidate->term?->variants->pluck('id') ?? []
            )
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param int[] $userCandidateIds
     */
    private function updateUserCandidates(array $userCandidateIds): void
    {
        UserTermCandidate::query()
            ->whereIn('id', $userCandidateIds)
            ->update([
                'is_processed' => true,
                'updated_at'   => now(),
            ]);
    }
}