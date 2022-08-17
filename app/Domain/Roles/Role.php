<?php

namespace App\Domain\Roles;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Clients\Projections\Client;
use App\Domain\Departments\Department;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\Locations\Projections\Location;
use App\Domain\Reminders\Reminder;
use App\Domain\Teams\Models\Team;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Users\Models\User;
use App\Models\File;
use App\Models\Folder;
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

    public static function getEntityFromGroup(string $group): ?string
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
                $entity = EmailTemplate::class;

                break;

            case 'sms-templates':
                $entity = SmsTemplate::class;

                break;

            case 'notes':
                $entity = Note::class;

                break;

            case 'folders':
                $entity = Folder::class;

                break;

            case 'drip-campaigns':
                $entity = DripCampaign::class;

                break;
        }

        return $entity;
    }

    public function scopeFilter($query, array $filters): void
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
