<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserProfilePictureMoved extends GymRevCrudEvent
{
    public const OPERATION = 'PROFILE_PICTURE_MOVED';
    public array $file;
    public ?array $old_file;

    public function __construct(array $file, ?array $old_file = null)
    {
        parent::__construct();
        $this->file     = $file;
        $this->old_file = $old_file;
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
