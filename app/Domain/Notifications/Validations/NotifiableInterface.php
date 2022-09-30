<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Validations;

interface NotifiableInterface
{
    /**
     * Notification payload is passed to this method for validation when "entity_type" is specified in notification
     * payload. Throw NotificationValidationException if validation fails.
     *
     * @param array $data
     *
     * @return void
     * @throws NotificationValidationException
     */
    public function entityDataValidation(array $data): void;
}
