<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Term\Mappers\TermDatasetMapper;
use App\Core\Modules\Term\Mappers\TranslationsDatasetMapper;
use App\Infrastructure\Modules\Ai\Contracts\InstructionSourceContract;
use App\Infrastructure\Modules\Ai\Sources\TxtFilesystemInstructionSource;
use App\Infrastructure\Modules\Config\Contracts\ConfigSourceContract;
use App\Infrastructure\Modules\Config\Sources\LaravelConfigSource;
use App\Infrastructure\Modules\Term\Contracts\TermsSourceContract;
use App\Infrastructure\Modules\Term\Contracts\TranslationsSourceContract;
use App\Infrastructure\Modules\Term\Sources\CsvFilesystemTermsSource;
use App\Infrastructure\Modules\Term\Sources\JsonFilesystemTranslationsSource;
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
        $this->app->bind(TermsSourceContract::class, function (Application $app) {
            return new CsvFilesystemTermsSource(
                $app->make(FilesystemFactory::class)->disk('dictionary'),
                $app->make(TermDatasetMapper::class),
            );
        });
        $this->app->bind(TranslationsSourceContract::class, function (Application $app) {
            return new JsonFilesystemTranslationsSource(
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
