<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Words\Enums\WordAnswerResult;
use App\Core\Modules\Words\Models\UserWordProgress;
use App\Core\Modules\Words\Tasks\CalculateWordProgressTask;
use Throwable;

final class EvaluateWordAnswerAction extends Action
{
    public function __construct(private readonly CalculateWordProgressTask $calculateTask){}

    /**
     * @throws Throwable
     */
    public function run(int $progressId, WordAnswerResult $result): void
    {
        $wordProgress = $this->getWordProgress($progressId);
        $wordProgress = $this->calculateTask->run($wordProgress, $result);

        $wordProgress->saveOrFail();
    }

    private function getWordProgress(int $progressId): UserWordProgress
    {
        return UserWordProgress::query()->findOrFail($progressId);
    }
}