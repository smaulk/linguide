<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Ai\Resolvers;

use App\Core\Modules\Ai\Dto\AiDriverConfigDto;
use App\Core\Modules\Ai\Enums\AiDriverType;
use App\Infrastructure\Common\Classes\ArrayReader;
use App\Infrastructure\Common\Parents\Resolver;
use App\Infrastructure\Modules\Config\Contracts\ConfigSourceContract;
use App\Infrastructure\Modules\Config\Exceptions\InvalidConfigurationException;
use Throwable;

final class AiDriverConfigResolver extends Resolver
{
    public function __construct(private readonly ConfigSourceContract $configSource){}

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