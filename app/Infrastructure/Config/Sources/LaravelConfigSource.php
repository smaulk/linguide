<?php
declare(strict_types=1);

namespace App\Infrastructure\Config\Sources;

use App\Infrastructure\Config\Exceptions\InvalidConfigurationException;
use App\Infrastructure\Config\Sources\Contracts\ConfigSourceContract;

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