<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class UpdateCandidateDto extends Dto
{
    public function __construct(
        public int $candidateId,
        public bool $isValid,
        public ?int $termId,
    ){}
}