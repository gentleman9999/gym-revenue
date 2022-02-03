<?php

namespace Database\Seeders;

use App\Models\Utility\AppState;
use Illuminate\Database\Seeder;

class AppStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppState::firstOrCreate([
            'name' => 'Simulation Mode',
            'slug' => 'is-simulation-mode',
            'desc' => 'When this mode is enabled, affected parts such as mass communications are not actually transmitted, but are logged and invoiced as if they did',
            'value' => 1,
        ]);
        // @todo - aggy, set the slug with the value and auto as the setter.
        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 01-26-2022',
            'value' => '2022-01-26',
            'misc' => [
                'buildno' => '20220126',
                'version' => '0.8.20',
                'notes' => [
                    'Fixed a bug in the dummy data seeder where clubs were missing City and State Data',
                    'City and States show up in the Club/Locations List View',
                    'There is now a State-level filter in Clubs/Locations',
                    'A Team Management CRUD is now available. Presently it is just a non-scoped view',
                    'A search filter is available in the Team Management CRUD',
                    'A User Management CRUD is now available.',
                    'A sidebar option for the User Management CRUD Is now available.',
                    'New User Management Filter - Search',
                    'New User Management Filter - Club-level Searching',
                    'New User Management Filter - Team-level Searching',
                    'Added a browser-tab set of day and night icons.',
                    'Updated the static content of the footer and Copyright Year as well as a copyright c before the year'
                ]
            ]
        ]);

        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 02-02-2022',
            'value' => '2022-02-02',
            'misc' => [
                'buildno' => '20220202',
                'version' => '0.9.22',
                'notes' => [
                    'Create Users and provide the basic info for their account in Account Management',
                    'You can assign a role to the user you are creating.'
                ]
            ]
        ]);
    }
}
