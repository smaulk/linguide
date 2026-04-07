<?php
declare(strict_types=1);

namespace App\Core\Modules\App\Classes;

use App\Core\Modules\App\Tasks\GetAppMetadataTask;
use App\Core\Modules\App\Tasks\SetAppMetadataTask;

final readonly class AccessManager
{
    private const string ACCESS_CONTROL_ENABLED = 'access_control_enabled';
    private const string ACCESS_CONTROL_CODE    = 'access_control_code';

    private const string TRUE  = '1';
    private const string FALSE = '0';

    public function __construct(
        private GetAppMetadataTask $getMetadataTask,
        private SetAppMetadataTask $setMetadataTask,
    ){}

    public function getAccessControl(): ?string
    {
        return $this->getMetadataTask->run(self::ACCESS_CONTROL_ENABLED);
    }

    public function isEnabledAccessControl(): bool
    {
        return $this->getAccessControl() === self::TRUE;
    }

    public function setAccessControl(bool $enabled): void
    {
        $this->setMetadataTask->run(
            self::ACCESS_CONTROL_ENABLED,
            $enabled ? self::TRUE : self::FALSE,
        );
    }

    public function getAccessCode(): ?string
    {
        $code = trim($this->getMetadataTask->run(self::ACCESS_CONTROL_CODE) ?? '');

        return $code !== '' ? $code : null;
    }

    public function setAccessCode(string $code): void
    {
        $this->setMetadataTask->run(self::ACCESS_CONTROL_CODE, $code);
    }

    public function isCorrectAccessCode(string $code): bool
    {
        $accessCode = $this->getAccessCode();
        if ($accessCode === null) {
            return false;
        }

        return trim($code) === $accessCode;
    }
}