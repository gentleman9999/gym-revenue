<?php

namespace App\Domain\EndUsers\Members\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;
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

    public static function getDetailsModel(): EndUserDetails
    {
        return new MemberDetails();
    }

    public function getInteractionCount()
    {
        $aggy = EndUserAggregate::retrieve($this->id);

        return $aggy->getInteractionCount();
    }

    public function isCBorGR(EndUser $user)
    {
        return (str_ends_with($user['email'], '@capeandbay.com') || str_ends_with($user['email'], '@gymrevenue.com'));
    }
}
