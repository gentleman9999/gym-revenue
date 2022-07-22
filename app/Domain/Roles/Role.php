<?php

namespace App\Domain\Roles;

use App\Domain\Clients\Models\Client;
use App\Domain\Departments\Department;
use App\Domain\Leads\Models\Lead;
use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\Reminders\Reminder;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Models\Calendar\CalendarEvent;
use App\Models\Clients\Location;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\Models\Endusers\Member;
use App\Models\File;
use App\Models\Note;
use App\Models\Position;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Silber\Bouncer\Database\Concerns\HasAbilities;
use Twilio\TwiML\Voice\Task;

class Role extends \Silber\Bouncer\Database\Role
{
    use HasFactory;
    use HasAbilities;
    use Sortable;

    protected $fillable = ['name', 'title', 'group'];

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
            case 'lead-statuses':
                $entity = LeadStatus::class;

                break;
            case 'lead-sources':
                $entity = LeadSource::class;

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
            case 'task':
                $entity = Task::class;

                break;
            case 'positions':
                $entity = Position::class;

                break;
            case 'departments':
                $entity = Department::class;

                break;
            case 'reminders':
                $entity = Reminder::class;

                break;
            case 'client':
                $entity = Client::class;

                break;
            case 'email-templates':
                $entity = EmailTemplates::class;

                break;
            case 'sms-templates':
                $entity = SmsTemplates::class;

                break;

            case 'notes':
                $entity = Note::class;

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
