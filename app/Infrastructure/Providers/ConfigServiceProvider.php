<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Infrastructure\Ai\Sources\Contracts\InstructionSourceContract;
use App\Infrastructure\Ai\Sources\FilesystemInstructionSource;
use App\Infrastructure\Config\Sources\Contracts\ConfigSourceContract;
use App\Infrastructure\Config\Sources\LaravelConfigSource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ConfigSourceContract::class, LaravelConfigSource::class);
        $this->app->bind(InstructionSourceContract::class, function () {
            return new FilesystemInstructionSource(
                Storage::disk('instructions'),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
