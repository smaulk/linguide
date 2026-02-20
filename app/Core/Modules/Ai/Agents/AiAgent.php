<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Agents;

use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Contracts\AiDriverContract;
use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use InvalidArgumentException;

final readonly class AiAgent implements AiAgentContract
{
    private const int DEFAULT_HISTORY_LIMIT = 20;

    private function __construct(
        private AiDriverContract $driver,
        private string $instruction,
        private int $historyLimit,
        private ?string $name = null,
    ){}

    public static function make(
        AiDriverContract $driver,
        string $instruction,
        ?int $historyLimit = null,
        ?string $name = null,
    ): self
    {
        if ($historyLimit === null) {
            $historyLimit = self::DEFAULT_HISTORY_LIMIT;
        }
        if ($historyLimit < 1) {
            throw new InvalidArgumentException('historyLimit must be greater than 0');
        }

        return new self($driver, $instruction, $historyLimit, $name);
    }


    public function send(array $messages): AiResponseDto
    {
        $request = new AiRequestDto($messages, $this->instruction);
        return $this->driver->send($request);
    }

    public function getHistoryLimit(): int
    {
        return $this->historyLimit;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}