<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserUpdatedCommunicationPreferences extends GymRevCrudEvent
{
    public const OPERATION = 'COMMUNICATION_PREFERENCES_UPDATED';
    public bool $email;
    public bool $sms;

    public function __construct(bool $email, bool $sms)
    {
        parent::__construct();
        $this->email = $email;
        $this->sms   = $sms;
    }

    public function getEntity(): string
    {
        return EndUser::class;
    }

    protected function getOperation(): string
    {
        return self::OPERATION;
    }
}
