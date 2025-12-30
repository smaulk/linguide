<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Drivers;

use GuzzleHttp\Client;
use App\Core\Modules\Ai\Contracts\AiDriverContract;
use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use Psr\Http\Message\ResponseInterface;

abstract class AiDriver implements AiDriverContract
{
    protected Client $client;

    public function __construct()
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

    abstract protected function callApi(array $payload): ResponseInterface;

    abstract protected function mapRequest(AiRequestDto $request): array;

    abstract protected function mapResponse(array $response): AiResponseDto;
}