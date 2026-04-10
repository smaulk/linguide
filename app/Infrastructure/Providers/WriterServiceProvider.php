<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Term\Mappers\TranslationsDatasetMapper;
use App\Infrastructure\Modules\Term\Contracts\TranslationsWriterContract;
use App\Infrastructure\Modules\Term\Writers\JsonFilesystemTranslationsWriter;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WriterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TranslationsWriterContract::class, function (Application $app) {
            return new JsonFilesystemTranslationsWriter(
                $app->make(FilesystemFactory::class)->disk('dictionary'),
                $app->make(TranslationsDatasetMapper::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}