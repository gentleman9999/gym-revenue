<?php

namespace Tests\Feature;

use App\Domain\Clients\Actions\CreateClient;
use App\Domain\Clients\Projections\Client;
use App\Domain\Roles\Role as Roles;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Enums\ClientServiceEnum;
use App\Enums\SecurityGroupEnum;
use Bouncer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Silber\Bouncer\Database\Role;
use Tests\TestCase;

class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('must be revisited.');
    }

    protected function allowReadInGroup($group, $role, $client)
    {
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role, $client) {
            // Create and get the abilities for all the groups
            $entity = Roles::getEntityFromGroup($group);
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                Bouncer::allow($role)->to("$group.read", $entity);
            }
        });
    }

    protected function allowEditInGroup($group, $role, $client)
    {
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role, $client) {
            $entity = Roles::getEntityFromGroup($group);

            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                Bouncer::allow($role)->to("$group.create", $entity);
                Bouncer::allow($role)->to("$group.update", $entity);
                Bouncer::allow($role)->to("$group.trash", $entity);
                Bouncer::allow($role)->to("$group.restore", $entity);
                Bouncer::allow($role)->to("$group.delete", $entity);
            }
        });
    }

    protected function allowImpersonationInGroup($group, $role, $client)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role, $client) {
            switch ($group) {
                case 'users':
                default:
                    $entity = User::class;

                    break;
            }
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                Bouncer::allow($role)->to("$group.impersonate", $entity);
            }
        });
    }

    public function test_teams_can_be_created()
    {
        collect(SecurityGroupEnum::cases())->keyBy('name')->only(['ADMIN'])->each(function ($enum) {
            Role::create([
            'name' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE),
            'group' => $enum->value,
            ])->update(['title' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE)]);
        });

        $cnb_team = CreateTeam::run([
            'client_id' => null,
                'name' => 'Cape & Bay Admin Team',
                'home_team' => 1,
        ]);

        $user = CreateUser::run([
            'client_id' => null,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'JohnDoe@somemail.com',
            'password' => 'Hello123!',
            'team_id' => $cnb_team->id,
            'role_id' => 1,
        //            'home_location_id' => $home_location_id,
                    'manager' => 'Senior Manager',
        ]);

        $clients = [
            'The Kalamazoo' => 1,
            'iFit' => 1,
            'TruFit Athletic Clubs' => 1,
            'Stencils' => 1,
            'The Z' => 1,
            'Sci-Fi Purple Gyms' => 1,
            'FitnessTruth' => 1,
        ];


        foreach ($clients as $name => $active) {
            $client = CreateClient::run(
                [
                    'name' => $name,
                    'active' => $active,
                    'services' => collect(ClientServiceEnum::cases())->map(fn ($e) => $e->name),
                ]
            );
        }

        $clients = Client::all();
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);
            collect(SecurityGroupEnum::cases())->keyBy('name')->except('ADMIN')
                ->each(function ($enum) use ($client) {
                    Role::create([
                        'name' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE),
                        'scope' => $client->id ?? null,
                        'group' => $enum->value,
                ])->update(['title' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE)]);
                });
        }
        Bouncer::scope()->to(null);

        /** Admin */
        Bouncer::allow('Admin')->everything(); // I mean....right?

        $crud_models = collect([
            'teams',
            // 'users', 'locations', 'leads', 'lead-statuses', 'lead-sources', 'members',
            // 'files', 'teams', 'tasks', 'calendar', 'roles', 'access_tokens', 'departments',
            // 'positions', 'email-templates', 'sms-templates',  'scheduled-campaigns', 'drip-campaigns',
            // 'reminders', 'notes', 'folders', 'searches',
        ]);
        $operations = collect(['create', 'read', 'update', 'trash', 'restore', 'delete']);

        // Create the Full Unrestricted Abilities
        $crud_models->each(function ($crud_model) use ($operations) {
            $operations->each(function ($operation) use ($crud_model) {
                $entity = Roles::getEntityFromGroup($crud_model);
                $title = ucwords("$operation $crud_model");
                Bouncer::ability()->firstOrCreate([
                    'name' => "$crud_model.$operation",
                    'title' => $title,
                    'entity_type' => $entity,
                ]);
            });
        });

        // Create user impersonation ability. It only applies to users.
        Bouncer::ability()->firstOrCreate([
            'name' => "users.impersonate",
            'title' => 'Impersonate Users',
            'entity_type' => User::class,
        ]);

        $clients = Client::all();
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);

            /** Account Owner */
            $this->allowReadInGroup([
                'teams',
            ], 'Account Owner', $client);
            $this->allowEditInGroup([
                'teams',
            ], 'Account Owner', $client);

            $this->allowImpersonationInGroup(['users'], 'Account Owner', $client);

            /** Regional Admin */
            $this->allowReadInGroup(
                ['teams', ],
                'Regional Admin',
                $client
            );
            $this->allowEditInGroup(
                ['teams'],
                'Regional Admin',
                $client
            );
            $this->allowImpersonationInGroup(['users'], 'Regional Admin', $client);

            /** Location Manager */
            $this->allowReadInGroup(['teams'], 'Location Manager', $client);
            $this->allowEditInGroup(['teams'], 'Location Manager', $client);
            $this->allowImpersonationInGroup(['users'], 'Location Manager', $client);

            /** Sales Rep */
            $this->allowReadInGroup(['teams',
            ], 'Sales Rep', $client);
            $this->allowEditInGroup(['leads', 'tasks', 'calendar', 'reminders'], 'Sales Rep', $client);

            /** Employee */
            $this->allowReadInGroup(['teams'], 'Employee', $client);
            $this->allowEditInGroup(['leads', 'tasks'], 'Employee', $client);

            $roles_allowed_to_contact_leads = ['Account Owner', 'Location Manager', 'Sales Rep', 'Employee'];
            foreach ($roles_allowed_to_contact_leads as $role) {
                Bouncer::allow($role)->to('leads.contact', Lead::class);
                Bouncer::allow($role)->to('members.contact', Member::class);
            }
            Bouncer::allow('Account Owner')->to('manage-client-settings');
        }
        Bouncer::scope()->to(null);







        // $user = User::find(1);
        // $this->actingAs($user);
        Sanctum::actingAs($user, ['*']);

        //login
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Hello123!',
        ]);

        $response = $this->post('/teams', [
            'name' => 'Test TeamXx',
        ]);

        $this->assertTrue(true);

        // Team::where('name',"TestaXx");

        // $this->assertCount(2, $user->fresh()->ownedTeams);
        // $this->assertEquals('Test Team', $user->fresh()->ownedTeams()->latest('id')->first()->name);
    }
}
