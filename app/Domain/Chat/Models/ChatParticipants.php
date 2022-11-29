<?php

declare(strict_types=1);

namespace App\Domain\Chat\Models;

use App\Domain\Users\Models\User;
use App\Models\GymRevUuidProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $chat_id
 * @property int    $user_id
 * @property User   $user
 */
class ChatParticipants extends GymRevUuidProjection
{
    use SoftDeletes;
    use Sortable;
    protected $hidden = ['client_id'];

    protected $fillable = ['chat_id', 'user_id'];

    public function chats(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getIdForChatUser(string $chat_id, int $user_id): string
    {
        return static::where(['user_id' => $user_id, 'chat_id' => $chat_id])->first()->id;
    }
}
