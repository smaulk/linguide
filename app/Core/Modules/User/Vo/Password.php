<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Vo;

use App\Core\Common\Parents\Vo;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;

final readonly class Password extends Vo
{
    private function __construct(public string $value) {}

    public static function fromString(string $password): self
    {
        if (mb_strlen($password) < 8) {
            throw new InvalidUserDataException('Password too short');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new InvalidUserDataException('Password must contain uppercase letter');
        }
        if (!preg_match('/\d/', $password)) {
            throw new InvalidUserDataException('Password must contain digit');
        }

        return new self($password);
    }
}