<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Words\Mappers\WordTranslationsMapper;
use App\Infrastructure\Ai\Sources\Contracts\InstructionSourceContract;
use App\Infrastructure\Ai\Sources\TxtFilesystemInstructionSource;
use App\Infrastructure\Config\Sources\Contracts\ConfigSourceContract;
use App\Infrastructure\Config\Sources\LaravelConfigSource;
use App\Infrastructure\Learning\Sources\Contracts\WordTranslationsSourceContract;
use App\Infrastructure\Learning\Sources\Contracts\WordSourceContract;
use App\Infrastructure\Learning\Sources\CsvFilesystemWordSource;
use App\Infrastructure\Learning\Sources\JsonFilesystemWordTranslationsSource;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

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
            );
        });
        $this->app->bind(WordTranslationsSourceContract::class, function (Application $app) {
            return new JsonFilesystemWordTranslationsSource(
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
