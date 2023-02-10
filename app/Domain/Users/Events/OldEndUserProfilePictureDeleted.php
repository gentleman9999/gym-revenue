<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevCrudEvent;

class OldEndUserProfilePictureDeleted extends GymRevCrudEvent
{
    public const OPERATION = 'OLD_PROFILE_PICTURE_DELETED';
    public array $file;

    public function __construct(array $file)
    {
        $this->file = $file;
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
