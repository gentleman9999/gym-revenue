<?php

namespace App\Domain\EndUsers\Leads\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $primary_phone
 * @property Client  $client
 */
class Lead extends EndUser
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'lead_type_id',
        'lead_source_id',
        'lead_status_id',
    ];

    public static function getDetailsModel(): EndUserDetails
    {
        return new EndUserDetails();
    }

    public function getPhoneNumber(): string
    {
        return $this->primary_phone;
    }
}
