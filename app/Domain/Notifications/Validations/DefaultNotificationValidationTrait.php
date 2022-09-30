<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Validations;

trait DefaultNotificationValidationTrait
{
    public function entityDataValidation(array $data): void
    {
        //throw new NotificationValidationException('...');
    }
}
