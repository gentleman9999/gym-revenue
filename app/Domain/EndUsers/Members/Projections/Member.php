<?php

namespace App\Domain\EndUsers\Members\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Projections\EndUser;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property Client $client
 */
class Member extends EndUser
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;
}
