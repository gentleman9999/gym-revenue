<?php

namespace App\Exceptions\Clients;

use Exception;

class ClientAccountException extends Exception
{
    public static function defaultTeamAlreadyCreated(string $default_team)
    {
        return new self("This account already has a default team - '{$default_team}' set");
    }

    public static function prefixAlreadyCreated(string $prefix, string $default_team)
    {
        return new self("This account already has a prefix ('{$prefix}') set for '{$default_team}'");
    }

    public static function teamAlreadyAssigned(string $team_name)
    {
        return new self("This team - '{$team_name}' has already been added");
    }

    public static function noCapeAndBayUsersAssigned()
    {
        return new self('There are no Cape & Bay users to add to this team. Add some before calling this function.');
    }
}
