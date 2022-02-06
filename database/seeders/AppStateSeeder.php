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
                    'You can assign a role to the user you are creating.',
                    'You can see how many times A Lead\'s free trial pass was used.',
                    'You can see when a lead\'s free trial expires',
                    'Agreement #s are generated everytime a lead is created',
                    'Agreement #s can be viewed in the lead editor or Sales Rep view',
                    'Lead Sources Management Page was created and is accessible in the Leads List View in the toolbar',
                    'Free Trial management is available in the settings menu inside of any client.'
                ]
            ]
        ]);

        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 02-06-2022',
            'value' => '2022-02-06',
            'misc' => [
                'buildno' => '20220206',
                'version' => '0.10.23',
                'notes' => [
                    'Added Middle name columns to Create and Update',
                    'The lead’s First, Middle & Last show up below the avatar in real time.',
                    'Added Opportunity dropdowns to Leads Create and Update',
                    'Added Gender dropdowns to Leads Create and Update',
                    'Added Date of Birth DatePicker to Leads Create and Update',
                    'Added Club/Location dropdowns to Leads Create and Update',
                    'A note of the when a lead was last edited by & when at the bottom of the edit page.',
                    'Added Lead Owner dropdowns to Create and Update. The selected owner, claims the lead.',
                    'Removed Membership Type and Services from Leads Create & Update',
                    'Updated the Lead Status dropdowns to Leads Create and Update',
                    'Added Lead Status dropdowns to Lead Create and Update',
                    'On Leads View, the toolbar link for it is highlighted',
                    'Added Team Management to SideBar',
                    'You can Create new Teams from the Management UI',
                    'You can Add Users into those new teams',
                    'You can add Locations to non-cape&bay teams',
                    'You can edit team configurations of Teams and Users.',
                    'The New team appears in your Switch Team Dropdown after creating a new team',
                    'You can now Create new Locations',
                    'You can Update Locations',
                    'New Location fields in Create & Edit - Phone, Open Date, Closed Date, PoC First, Last  Phone, and an Arbitrary Location No',
                ]
            ]
        ]);
    }
}
