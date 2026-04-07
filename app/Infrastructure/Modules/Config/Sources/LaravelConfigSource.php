<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Config\Sources;

use App\Infrastructure\Modules\Config\Contracts\ConfigSourceContract;
use App\Infrastructure\Modules\Config\Exceptions\InvalidConfigurationException;

final class LaravelConfigSource implements ConfigSourceContract
{
    public function get(string $key): array
    {
        $config = config($key);
        if (!is_array($config)) {
            throw new InvalidConfigurationException("Config not found: {$key}");
        }

        return $config;
    }
}