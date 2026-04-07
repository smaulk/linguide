<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Tasks\GetWordTranslationsTask;
use App\Infrastructure\Modules\Dictionary\Contracts\WordTranslationsWriterContract;

final class ExportWordTranslationsAction extends Action
{
    public function __construct(
        private readonly GetWordTranslationsTask $getTask,
        private readonly WordTranslationsWriterContract $translationsWriter,
    ){}

    public function run(string $resourceName): int
    {
        $wordsCount = 0;

        foreach ($this->getTask->run() as $words) {
            $wordsCount += count($words);

            $this->translationsWriter->write($resourceName, $words);
        }

        return $wordsCount;
    }
}