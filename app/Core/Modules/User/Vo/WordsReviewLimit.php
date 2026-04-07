<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Vo;

use App\Core\Common\Parents\Vo;
use InvalidArgumentException;

final readonly class WordsReviewLimit extends Vo
{
    private const int MIN = 1;

    private function __construct(private int $value){}

    public static function fromInt(int $limit): self
    {
        if($limit < self::MIN) {
            throw new InvalidArgumentException("Invalid words review limit: $limit");
        }

        return new self($limit);
    }

    public function value(): int
    {
        return $this->value;
    }
}