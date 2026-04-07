<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Ai\Resolvers;

use App\Core\Modules\Ai\Dto\AiAgentConfigDto;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\Ai\Enums\AiDriverType;
use App\Infrastructure\Common\Classes\ArrayReader;
use App\Infrastructure\Common\Parents\Resolver;
use App\Infrastructure\Modules\Ai\Contracts\InstructionSourceContract;
use App\Infrastructure\Modules\Config\Contracts\ConfigSourceContract;
use App\Infrastructure\Modules\Config\Exceptions\InvalidConfigurationException;
use Throwable;

final class AiAgentConfigResolver extends Resolver
{
    public function __construct(
        private readonly ConfigSourceContract $configSource,
        private readonly InstructionSourceContract $instructionSource,
    ){}

    /**
     * @throws InvalidConfigurationException
     */
    public function resolve(AiAgentType $agent): AiAgentConfigDto
    {
        $baseConfig = new ArrayReader($this->configSource->get('ai'));
        $agentConfig = new ArrayReader($this->configSource->get("ai.agents.{$agent->value}"));

        try {
            $defaultDriver = $baseConfig->string('default_driver');
            $driver = $agentConfig->string('driver', $defaultDriver);
            $instructionName = $agentConfig->string('instruction');

            return new AiAgentConfigDto(
                instruction: $this->instructionSource->get($instructionName),
                driverType: $this->getDriverType($driver),
                historyLimit: $agentConfig->nullableInt('history_limit'),
                name: $agentConfig->nullableString('name'),
            );
        } catch (Throwable $e) {
            throw new InvalidConfigurationException(
                message: "Invalid configuration for AI agent '{$agent->value}': {$e->getMessage()}",
                previous: $e
            );
        }
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function getDriverType(string $driver): AiDriverType
    {
        $driverType = AiDriverType::tryFrom($driver);
        if ($driverType === null) {
            throw new InvalidConfigurationException("Unsupported AI driver: {$driver}");
        }

        return $driverType;
    }
}