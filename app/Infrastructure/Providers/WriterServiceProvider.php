<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Words\Mappers\WordTranslationsMapper;
use App\Infrastructure\Learning\Writers\Contracts\WordTranslationsWriterContract;
use App\Infrastructure\Learning\Writers\JsonFilesystemWordTranslationsWriter;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class WriterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WordTranslationsWriterContract::class, function (Application $app) {
            return new JsonFilesystemWordTranslationsWriter(
                $app->make(FilesystemFactory::class)->disk('translations'),
                $app->make(WordTranslationsMapper::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}