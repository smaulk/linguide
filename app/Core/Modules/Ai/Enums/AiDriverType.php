<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Enums;

enum AiDriverType: string
{
    case GEMINI = 'gemini';

    /**
     * @return array<string, string>
     */
    public function defaultConfig(): array
    {
        return match ($this) {
            self::GEMINI => [
                'apiKey'     => config('ai.drivers.gemini.api_key'),
                'apiVersion' => config('ai.drivers.gemini.api_version'),
                'model'      => config('ai.drivers.gemini.model'),
            ],
        };
    }
}
