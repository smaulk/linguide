<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Tasks\GetTermTranslationsTask;
use App\Infrastructure\Modules\Term\Contracts\TranslationsWriterContract;

final class ExportTranslationsAction extends Action
{
    public function __construct(
        private readonly GetTermTranslationsTask $getTask,
        private readonly TranslationsWriterContract $translationsWriter,
    ){}

    public function run(string $resourceName): int
    {
        $exportedCount = 0;

        foreach ($this->getTask->run() as $terms) {
            $exportedCount += count($terms);
            $this->translationsWriter->write($resourceName, $terms);
        }

        return $exportedCount;
    }
}