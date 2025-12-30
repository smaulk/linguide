<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Drivers;

use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use App\Core\Modules\Ai\Dto\GeminiConfigDto;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;


final class GeminiDriver extends AiDriver
{
    private const string BASE_URI = 'https://generativelanguage.googleapis.com';

    public function __construct(private readonly GeminiConfigDto $config)
    {
        parent::__construct();
    }

    protected function makeClient(): Client
    {
        return new Client([
            'base_uri' => self::BASE_URI,
            'headers'  => [
                'Content-Type'   => 'application/json',
                'X-goog-api-key' => $this->config->apiKey,
            ],
        ]);
    }

    protected function callApi(array $payload): ResponseInterface
    {
        $modelPath = "/{$this->config->apiVersion}/models/{$this->config->model}";

        return $this->client->post($modelPath . ':generateContent', [
            'json' => $payload,
        ]);
    }


    protected function mapRequest(AiRequestDto $request): array
    {
        return array_filter([
            'contents' => $this->wrapTextInParts($request->message),
            'system_instruction' => $request->instruction
                ? $this->wrapTextInParts($request->instruction)
                : null,
        ]);
    }

    private function wrapTextInParts(string $text): array
    {
        return [
            'parts' => [
                ['text' => $text]
            ],
        ];
    }

    protected function mapResponse(array $response): AiResponseDto
    {
        $candidate = $response['candidates'][0];

        return new AiResponseDto(
            text: $candidate['content']['parts'][0]['text'],
            role: $candidate['content']['role'],
            finishReason: $candidate['finishReason'],
        );
    }
}