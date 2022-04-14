<?php

namespace App\Models;

use App\Models\Calendar\CalendarEvent;
use App\Models\Clients\Classification;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Traits\Sortable;
use Bouncer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Silber\Bouncer\Database\Role
{
    use HasFactory;
    use Sortable;

    protected $fillable = ['name', 'title', 'group', 'id'];

    public static function getEntityFromGroup(string $group)
    {
        $entity = null;
        switch($group)
        {
            case 'users':
                $entity = User::class;
                break;
            case 'locations':
                $entity = Location::class;
                break;
            case 'leads':
                $entity = Lead::class;
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
            case 'todo-list':
                $entity = TodoList::class;
                break;
        }

        return $entity;
    }

}
