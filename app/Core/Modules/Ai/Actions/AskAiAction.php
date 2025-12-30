<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Actions;

use App\Core\Common\Base\Action;
use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Dto\AiResponseDto;
use App\Core\Modules\Ai\Dto\AskAiDto;
use App\Core\Modules\Ai\Factories\AiDriverFactory;

final class AskAiAction extends Action
{
    public function run(AskAiDto $dto): AiResponseDto
    {
        $provider = AiDriverFactory::make($dto->providerType, $dto->providerConfig);

        return $provider->send(new AiRequestDto($dto->message));
    }
}