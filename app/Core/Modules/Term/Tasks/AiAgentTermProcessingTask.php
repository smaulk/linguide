<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Ai\Contracts\AiAgentContract;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use Illuminate\Support\Facades\Log;
use JsonException;

abstract class AiAgentTermProcessingTask extends Task
{
    /**
     * @throws AiUnavailableException
     */
    private function sendToAgent(AiAgentContract $agent, string $termsMessage): string
    {
        $response = $agent->send(new AiMessageDto(content: $termsMessage));

        return $response->text;
    }

    /**
     * @param AiAgentContract $agent
     * @param mixed[] $items
     * @return list<array<string, mixed>>|null
     * @throws AiUnavailableException
     */
    protected function processChunk(AiAgentContract $agent, array $items): ?array
    {
        $prepared = $this->serializeItems($items);
        if ($prepared === false) {
            return null;
        }

        $response = $this->sendToAgent($agent, $prepared);

        try {
            return $this->parseDataArrayFromResponse($response);
        } catch (JsonException $e) {
            Log::warning(static::class . ' json decode failed.', ['exception' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * @return list<array<string, mixed>>|null
     * @throws JsonException
     */
    private function parseDataArrayFromResponse(string $response): ?array
    {
        $json = trim($response);

        if (str_starts_with($json, '```')) {
            $json = preg_replace('/^```[a-z]*\s*|\s*```$/i', '', $json) ?? $json;
        }

        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed[] $items
     * @return string|false
     */
    abstract protected function serializeItems(array $items): string|false;
}