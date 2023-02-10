<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Models;

use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockDeleted;
use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockUpdated;
use App\Models\GymRevProjection;

/**
 * @property string $id
 * @property string $client_id
 * @property string $name
 * @property string $definition
 * @property int    $user_id
 */
class EmailTemplateBlock extends GymRevProjection
{
    /** @var array<string> */
    protected $fillable = [
        'name',
        'definition',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'definition' => 'json',
    ];

    public function edit(array $attributes = [], array $options = []): bool
    {
        event((new EmailTemplateBlockUpdated($attributes, $options))->setUid($this->id));

        return true;
    }

    public function remove(): bool
    {
        event((new EmailTemplateBlockDeleted())->setUid($this->id));

        return true;
    }
}
