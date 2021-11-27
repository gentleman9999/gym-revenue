<?php

namespace App\Models\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class EmailTemplates extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'markup', 'active', 'team_id', 'created_by_user_id'
    ];

    // @todo - model boot created - aggy to set in client_details and email_template_details that it was auto generated ('auto' created_by_user_id)
    protected static function booted()
    {
        static::created(function ($template) {
            if(!is_null($template->client_id))
            {
                ClientAggregate::retrieve($template->client_id)
                    ->createNewEmailTemplate($template->id,$template->created_by_user_id)
                    ->persist();
            }
            else {
                $detail = EmailTemplateDetails::create([
                    'email_template_id' => $template->id,
                    'detail' => 'created',
                    'value' => $template->created_by_user_id,
                ]);
                if($template->created_by_user_id == 'auto')
                {
                    $detail->misc = ['msg' => 'Template was auto-generated'];
                }
                else
                {
                    $user = User::find($template->created_by_user_id);
                    $detail->misc = ['msg' => 'Template was created by '.$user->name.' on '.date('Y-m-d')];
                }
            }

        });

        // @todo - model boot updated - aggy to set in client_details and email_template_details that it was auto generated ('auto' created_by_user_id)
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('created_by_user_id', 'like', '%' . $search . '%')
                ;
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}