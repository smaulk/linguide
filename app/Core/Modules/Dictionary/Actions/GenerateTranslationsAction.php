<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Tasks\GenerateTranslationsTask;
use App\Infrastructure\Modules\Dictionary\Contracts\TranslationsWriterContract;
use Illuminate\Contracts\Container\BindingResolutionException;

final class GenerateTranslationsAction extends Action
{
    public function __construct(
        private readonly GenerateTranslationsTask $generateTask,
        private readonly TranslationsWriterContract $translationsWriter,
    ){}

    /**
     * @throws BindingResolutionException
     */
    public function run(string $resourceName, bool $isOnlyEmpty): int
    {
        $generatedCount = 0;

        foreach ($this->generateTask->run($isOnlyEmpty) as $terms) {
            $generatedCount += count($terms);
            $this->translationsWriter->write($resourceName, $terms);
        }

        return $generatedCount;
    }
}