<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Contracts;

use App\Core\Modules\Ai\Dto\AiRequestDto;
use App\Core\Modules\Ai\Dto\AiResponseDto;

interface AiDriverContract
{
    public function send(AiRequestDto $request): AiResponseDto;
}