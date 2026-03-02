<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Drivers;

use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use Illuminate\Http\Client\Response;

final class GeminiDriver extends AiDriver
{
    protected function headers(): array
    {
        return ['X-goog-api-key' => $this->config->apiKey];
    }

    protected function callApi(array $payload): Response
    {
        $modelPath = "/{$this->config->apiVersion}/models/{$this->config->model}";
        return $this->request()->post($modelPath . ':generateContent', $payload);
    }

    protected function mapRequest(AiRequestDto $request): array
    {
        return array_filter([
            'contents'           => $this->prepareRequestContents($request),
            'system_instruction' => $request->instruction
                ? ['parts' => $this->wrapText($request->instruction)]
                : null,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function prepareRequestContents(AiRequestDto $request): array
    {
        $result = [];
        foreach ($request->messages as $message) {
            $result[] = [
                'role'  => $this->resolveRole($message->role),
                'parts' => $this->wrapText($message->content)
            ];
        }

        return $result;
    }

    /**
     * @return list<array<string, string>>
     */
    private function wrapText(string $text): array
    {
        return [['text' => $text]];
    }

    private function resolveRole(AiMessageRole $role): string
    {
        return match ($role) {
            AiMessageRole::ASSISTANT => 'model',
            default                  => $role->value,
        };
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