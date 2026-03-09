<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Words\Tasks\GenerateWordTranslationsTask;
use App\Infrastructure\Learning\Writers\Contracts\WordTranslationsWriterContract;
use Illuminate\Contracts\Container\BindingResolutionException;

final class GenerateWordTranslationsAction extends Action
{
    public function __construct(
        private readonly GenerateWordTranslationsTask $generateTask,
        private readonly WordTranslationsWriterContract $translationsWriter,
    ){}

    /**
     * @throws BindingResolutionException
     */
    public function run(string $resourceName, bool $isOnlyEmpty): int
    {
        $wordsCount = 0;

        foreach ($this->generateTask->run($isOnlyEmpty) as $words) {
            $wordsCount += count($words);

            $this->translationsWriter->write($resourceName, $words);
        }

        return $wordsCount;
    }
}