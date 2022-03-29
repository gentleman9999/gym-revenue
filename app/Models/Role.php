<?php

namespace App\Models;

use App\Models\Clients\Classification;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Silber\Bouncer\Database\Role
{
    use HasFactory;

    protected $fillable = ['name', 'title', 'client_id'];

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
