<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Drivers;

use App\Core\Modules\Ai\Dto\AiDriverConfigDto;
use App\Core\Modules\Ai\Dto\AiRequestDto;
use GuzzleHttp\Client;
use App\Core\Modules\Ai\Contracts\AiDriverContract;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use Psr\Http\Message\ResponseInterface;

abstract class AiDriver implements AiDriverContract
{
    protected Client $client;

    public function __construct(protected readonly AiDriverConfigDto $config)
    {
        $this->client = $this->makeClient();
    }

    final public function send(AiRequestDto $request): AiResponseDto
    {
        $payload = $this->mapRequest($request);
        $response = $this->callApi($payload);
        $raw = json_decode($response->getBody()->getContents(), true);

        return $this->mapResponse($raw);
    }

    abstract protected function makeClient(): Client;

    /**
     * @param array<string, mixed> $payload
     * @return ResponseInterface
     */
    abstract protected function callApi(array $payload): ResponseInterface;

    /**
     * @param AiRequestDto $request
     * @return array<string, mixed>
     */
    abstract protected function mapRequest(AiRequestDto $request): array;

    /**
     * @param array<string, mixed> $response
     * @return AiResponseDto
     */
    abstract protected function mapResponse(array $response): AiResponseDto;
}