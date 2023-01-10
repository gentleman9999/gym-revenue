<?php

namespace App\Models\GatewayProviders;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * @property string $id
 */
class GatewayProvider extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;
    public const PROVIDER_SLUG_TWILIO_VOICE = 'twilio-voice';
    public const PROVIDER_SLUG_TWILIO_SMS = 'twilio-sms';
    public const PROVIDER_SLUG_TWILIO_CONVERSION = 'twilio-conversation';
    public const PROVIDER_SLUG_MAILGUN = 'mailgun';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'desc',
        'vendor',
        'provider_type',
        'profile_class',
        'provider_rate',
        'gr_commission_rate',
        'gr_commission_bulk_rate',
        'details',
        'active',
        'misc',
    ];

    protected $casts = [
        'details' => AsCollection::class,
        'misc' => 'array',
    ];

    public static function getAllProvidersAsArray()
    {
        $results = [];

        $records = self::whereActive(1)->get();

        foreach ($records as $record) {
            $results[$record->slug] = $record->toArray();
        }

        return $results;
    }

    public function addOrUpdateDetails(string $field, ?string $value, $misc = null, $active = 1): void
    {
        $detail = [
            'field' => $field,
            'value' => $value,
            'misc' => $misc,
            'active' => $active,
        ];

        $details = $this->details ?: new Collection();

        if ($details->where('field', $field)->count() > 0) {
            $details = $details->transform(
                function ($item, $key) use ($field, $detail) {
                    if ($item['field'] === $field) {
                        return $detail;
                    }

                    return $item;
                }
            );
        } else {
            $details->push($detail);
        }

        $this->details = $details;
    }
}
