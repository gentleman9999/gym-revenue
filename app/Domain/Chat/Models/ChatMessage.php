<?php

declare(strict_types=1);

namespace App\Domain\Chat\Models;

use App\Models\GymRevUuidProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string          $id
 * @property string          $chat_id
 * @property string          $chat_participant_id
 * @property string          $message
 * @property ChatParticipant $participant
 * @property Chat            $chat
 */
class ChatMessage extends GymRevUuidProjection
{
    use SoftDeletes;
    use Sortable;

    protected $hidden = ['client_id'];

    protected $fillable = ['id', 'chat_id', 'chat_participant_id', 'message', 'read_by'];

    protected $casts = [
        'read_by' => 'array',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(ChatParticipant::class, 'chat_participant_id', 'id');
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
}
