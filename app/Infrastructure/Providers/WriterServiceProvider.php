<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Dictionary\Mappers\WordTranslationsDatasetMapper;
use App\Infrastructure\Modules\Dictionary\Contracts\WordTranslationsWriterContract;
use App\Infrastructure\Modules\Dictionary\Writers\JsonFilesystemWordTranslationsWriter;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WriterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WordTranslationsWriterContract::class, function (Application $app) {
            return new JsonFilesystemWordTranslationsWriter(
                $app->make(FilesystemFactory::class)->disk('translations'),
                $app->make(WordTranslationsDatasetMapper::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}