<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Drivers;

use App\Core\Modules\Ai\Dto\AiDriverConfigDto;
use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Contracts\AiDriverContract;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

abstract class AiDriver implements AiDriverContract
{
    public function __construct(
        private readonly HttpFactory $http,
        protected readonly AiDriverConfigDto $config,
    ){}

    /**
     * @throws ConnectionException
     */
    final public function send(AiRequestDto $request): AiResponseDto
    {
        $payload = $this->mapRequest($request);
        $response = $this->callApi($payload);
        $raw = $response->json();

        return $this->mapResponse($raw);
    }

    /**
     * Экземпляр запроса
     */
    protected function request(): PendingRequest
    {
        /** @var PendingRequest */
        return $this->http
            ->baseUrl($this->config->baseUrl)
            ->withHeaders($this->headers())
            ->acceptJson()
            ->asJson()
            ->retry(3, 1000)
            ->connectTimeout(5)
            ->timeout(30);
    }

    /**
     * Заголовки запроса
     * @return array<string, mixed>
     */
    abstract protected function headers(): array;

    /**
     * @param array<string, mixed> $payload
     * @return Response
     * @throws ConnectionException
     */
    abstract protected function callApi(array $payload): Response;

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