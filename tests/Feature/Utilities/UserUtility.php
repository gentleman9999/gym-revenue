<?php

declare(strict_types=1);

namespace Tests\Feature\Utilities;

use App\Domain\Clients\Projections\Client;
use App\Domain\Roles\Actions\CreateRole;
use App\Domain\Roles\Role;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;

class UserUtility
{
    /**
     * @param int $count
     *
     * @return User|array<User>
     */
    public static function createUserWithTeam(int $count = 1): User|array
    {
        $team = CreateTeam::run(Team::factory()->raw(['client_id' => null]));

        return self::createUser(['team_id' => $team->id, 'client_id' => $team->client_id], $count);
    }

    /**
     * @param array $attribute
     * @param int   $count
     *
     * @return User|array<User>
     */
    public static function createUser(array $attribute = [], int $count = 1): User|array
    {
        $users = [];
        if (! array_key_exists('client_id', $attribute)) {
            $attribute['client_id'] = Client::factory()->create()->id;
        }

        if (! array_key_exists('team_id', $attribute)) {
            $attribute['team_id'] = Team::factory()->create()->id;
        }

        for ($i = 0; $i < $count; $i++) {
            $users[] = CreateUser::run($attribute + User::factory()->raw(['password' => 'Hello123!']));
        }

        return $count === 1 ? $users[0] : $users;
    }

    /**
     * @param array $attribute
     * @param int   $count
     *
     * @return User|array<User>
     */
    public static function createUserWithoutTeam(array $attribute = [], int $count = 1): User|array
    {
        $users = [];
        if (! array_key_exists('client_id', $attribute)) {
            $attribute['client_id'] = Client::factory()->create()->id;
        }

        for ($i = 0; $i < $count; $i++) {
            $users[] = CreateUser::run($attribute + User::factory()->raw(['password' => 'Hello123!']));
        }

        return $count === 1 ? $users[0] : $users;
    }

    /**
     * @param array $attributes
     *
     * @return Role
     */
    public static function createRole(array $attributes = []): Role
    {
        return CreateRole::run($attributes + Role::factory()->raw() + [
            'group' => rand(1, 10),
            'ability_names' => ['update.users', 'read.users', 'create.users', 'delete.users'],
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
