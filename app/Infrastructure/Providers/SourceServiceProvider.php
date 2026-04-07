<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Dictionary\Mappers\WordImportMapper;
use App\Core\Modules\Dictionary\Mappers\WordTranslationsDatasetMapper;
use App\Infrastructure\Modules\Ai\Contracts\InstructionSourceContract;
use App\Infrastructure\Modules\Ai\Sources\TxtFilesystemInstructionSource;
use App\Infrastructure\Modules\Config\Contracts\ConfigSourceContract;
use App\Infrastructure\Modules\Config\Sources\LaravelConfigSource;
use App\Infrastructure\Modules\Dictionary\Contracts\WordSourceContract;
use App\Infrastructure\Modules\Dictionary\Contracts\WordTranslationsSourceContract;
use App\Infrastructure\Modules\Dictionary\Sources\CsvFilesystemWordSource;
use App\Infrastructure\Modules\Dictionary\Sources\JsonFilesystemWordTranslationsSource;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SourceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ConfigSourceContract::class, LaravelConfigSource::class);
        $this->app->bind(InstructionSourceContract::class, function (Application $app) {
            return new TxtFilesystemInstructionSource(
                $app->make(FilesystemFactory::class)->disk('instructions'),
            );
        });
        $this->app->bind(WordSourceContract::class, function (Application $app) {
            return new CsvFilesystemWordSource(
                $app->make(FilesystemFactory::class)->disk('words'),
                $app->make(WordImportMapper::class),
            );
        });
        $this->app->bind(WordTranslationsSourceContract::class, function (Application $app) {
            return new JsonFilesystemWordTranslationsSource(
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
