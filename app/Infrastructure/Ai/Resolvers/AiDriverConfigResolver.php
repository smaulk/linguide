<?php
declare(strict_types=1);

namespace App\Infrastructure\Ai\Resolvers;

use App\Core\Modules\Ai\Dto\AiDriverConfigDto;
use App\Core\Modules\Ai\Enums\AiDriverType;
use App\Infrastructure\Config\Exceptions\InvalidConfigurationException;
use App\Infrastructure\Config\Sources\Contracts\ConfigSourceContract;
use App\Infrastructure\Support\ArrayReader;
use Throwable;

final readonly class AiDriverConfigResolver
{
    public function __construct(private ConfigSourceContract $configSource){}

    /**
     * @throws InvalidConfigurationException
     */
    public function resolve(AiDriverType $driver): AiDriverConfigDto
    {
        $config = new ArrayReader($this->configSource->get("ai.drivers.{$driver->value}"));

        try {
            return new AiDriverConfigDto(
                baseUrl: $config->string('base_url'),
                apiKey: $config->string('api_key'),
                model: $config->string('model'),
                apiVersion: $config->nullableString('api_version')
            );
        } catch (Throwable $e) {
            throw new InvalidConfigurationException(
                message: "Invalid configuration for AI driver '{$driver->value}': {$e->getMessage()}",
                previous: $e
            );
        }
    }
}