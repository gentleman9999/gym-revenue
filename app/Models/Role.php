<?php

namespace App\Models;

use App\Domain\Clients\Models\Client;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Models\Calendar\CalendarEvent;
use App\Models\Clients\Classification;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Endusers\Member;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Twilio\TwiML\Voice\Task;

class Role extends \Silber\Bouncer\Database\Role
{
    use HasFactory;
    use Sortable;

    protected $fillable = ['name', 'title', 'group', 'id'];

    public static function getEntityFromGroup(string $group)
    {
        $entity = null;
        switch ($group) {
            case 'users':
                $entity = User::class;

                break;
            case 'locations':
                $entity = Location::class;

                break;
            case 'leads':
                $entity = Lead::class;

                break;
            case 'members':
                $entity = Member::class;

                break;
            case 'teams':
                $entity = Team::class;

                break;
            case 'files':
                $entity = File::class;

                break;
            case 'calendar':
                $entity = CalendarEvent::class;

                break;
            case 'roles':
                $entity = Role::class;

                break;
            case 'classifications':
                $entity = Classification::class;

                break;
            case 'task':
                $entity = Task::class;

                break;
            case 'client':
                $entity = Client::class;

                break;
        }

        return $entity;
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
                $query->where('title', 'like', '%' . $search . '%');
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
