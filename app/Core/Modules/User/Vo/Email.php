<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Vo;

use App\Core\Common\Parents\Vo;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;

final readonly class Email extends Vo
{
    private function __construct(public string $value) {}

    public static function fromString(string $email): self
    {
        $email = mb_strtolower(trim($email));

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidUserDataException('Invalid email format');
        }

        return new self($email);
    }
}