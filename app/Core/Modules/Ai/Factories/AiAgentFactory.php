<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Factories;

use App\Core\Modules\Ai\Agents\AiAgent;
use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Infrastructure\Ai\Resolvers\AiAgentConfigResolver;
use Illuminate\Contracts\Container\BindingResolutionException;

final readonly class AiAgentFactory
{
    public function __construct(
        private AiAgentConfigResolver $configResolver,
        private AiDriverFactory $driverFactory,
    ){}

    /**
     * @throws BindingResolutionException
     */
    public function make(AiAgentType $agentType): AiAgentContract
    {
        $config = $this->configResolver->resolve($agentType);
        $driver = $this->driverFactory->make($config->driverType);

        return AiAgent::make(
            driver: $driver,
            instruction: $config->instruction,
            historyLimit: $config->historyLimit,
            name: $config->name,
        );
    }
}