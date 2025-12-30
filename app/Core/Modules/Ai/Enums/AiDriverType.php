<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Enums;

enum AiDriverType
{
    case GEMINI;

    public function defaultConfig(): array
    {
        return match ($this) {
            self::GEMINI => [
                'apiKey'     => config('ai.providers.gemini.api_key'),
                'apiVersion' => config('ai.providers.gemini.api_version'),
                'model'      => config('ai.providers.gemini.model'),
            ],
        };
    }
}
