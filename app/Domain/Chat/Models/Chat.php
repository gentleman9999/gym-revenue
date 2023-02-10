<?php

declare(strict_types=1);

namespace App\Domain\Chat\Models;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * @property string                      $client_id
 * @property int                         $created_by
 * @property string                      $id
 * @property Collection<ChatParticipant> $participants
 * @property array                       $read_by
 * @property int                         $admin_id
 */
class Chat extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    protected $hidden = ['client_id'];

    /** @var array<string>  */
    protected $fillable = ['client_id', 'created_by'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    /**
     * Check if chat already exist for specified use ids.
     *
     * @param array<int> $user_ids
     *
     */
    public static function findChatForUsers(array $user_ids): ?string
    {
        /** @var Builder $sub_query */
        $sub_query = ChatParticipant::select(
            'chat_id',
            DB::raw('GROUP_CONCAT(user_id ORDER BY user_id ASC) users')
        )->groupBy('chat_id');
        // Sort our user ids to match "ORDER BY user_id ASC"
        sort($user_ids, SORT_NUMERIC);

        return DB::query()
            ->from($sub_query, 's')
            ->where('users', implode(',', $user_ids))
            ->first()
            ?->chat_id;
    }
}
