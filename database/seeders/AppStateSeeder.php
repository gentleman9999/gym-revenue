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

        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 02-11-2022',
            'value' => '2022-02-11',
            'misc' => [
                'buildno' => '20220211',
                'version' => '0.10.25',
                'notes' => [
                    'Backend - New Clubs have a GymRevenue ID auto generated',
                    'Phone # is now a field in Create and Edit Users',
                    'Employee is now a new Role for making Security role templates',
                    'A link to security roles is available on the All users view',
                    'Updates to creating user to support a registration flow - removed password from create',
                    'Swap out Roles dropdown with a Security Role dropdown',
                    'UI to Assign users and club locations to a team',
                    'Security Roles is now an entity in the DB with default security roles for Regional Manager, Location Manager, Sales Rep and Employee',
                    'A security Roles CRUD is now available',
                    'Implemented preliminary Access Control -',
                    '* - The Sales Rep view in the Leads CRUD in only accessible via users with a Location Managers and Sales Rep level security role',
                    '* - The User Management view is accessible to Cnb Admins and Account Owners',
                    '* - The Team Management view is accessible to Cnb Admins and Account Owners',
                    '* - Club Management should only show Add, Edit and Delete links to Account Owners and CnB Admins Only',
                    '* - Club Management View is read-only for all other roles',
                    '* - File Manager only appears to Cape & Bay admins',
                    '* - Workout Generator got moves to the Name dropdown on the upper right',
                    '* - ToDo list Coming Soon link only appears for users with Sales Reps, Location Manager and Employee Security Roles',
                    '* - The settings Link on the sidebar works',
                    '* - Team Management only shows a Client’s teams instead of all teams within GR',
                    'Label copy on Create and Update Users updated',
                    'Label copy on Update Club/Locations to say Club’s name and GymRevenue ID',
                    'Updated the Navigation on CRUD with a page Nav Tool Bar to highlight the active page.',
                    'Lead Statuses UI is now available from the Leads Dashboard',
                    'Individual First & Last Name in User Management Create and Update',
                ]
            ]
        ]);

        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 02-23-2022',
            'value' => '2022-02-23',
            'misc' => [
                'buildno' => '20220223',
                'version' => '0.12.30',
                'notes' => [
                    'Dashboard Placeholder UI Refresh',
                    '* - The active team is visible from the dashboard',
                    '* - Cape & Bay teams are now known as GymRevenue teams. The names have not changed though.',
                    '* - GymRevenue Users inside a GymRevenue team see a team switcher on the left side  of the dash and The latest updates announcement on the right.',
                    '* - The Cape & Bay Developers team has the teams switcher widget on the left and as TBD widget of tickets assigned to that developer',
                    '* - Client Teams also have a refresh',
                    '** - Account Owners, GR Admins, and Regional Managers see TBD widgets of Club/Locations and Sales Charts',
                    '** - Location Managers see an empty todo list and club employee widgets',
                    '** - Sales Reps and Employees see an empty Latest Claimable leads widget an empty todo list widget',
                    'Mass Comm - You can now send yourself a test Text message of SMS Templates when inside of a Client team',
                    '* - You must add a phone number in the Profile Manager page',
                    'Mass Comm - You can now send yourself a text email of Email Templates when inside of a Client team',
                    'User Management - User Preview is now available',
                    'User Management - Create/Update: Swapped out Role Select for Security Role Select',
                    'User Management - Create/Update: New Columns',
                    '* - Personal Email',
                    '* - Address 1 & 2',
                    '* - City, State, Zip',
                    '* - Job Title',
                    'User Management - Update: Updated Business Rules',
                    '* - Trying to edit yourself redirects you to the Profile Management page',
                    '* - You can’t delete an Account Owner',
                    '* - Users have to be granted the ability to edit leads within their security role',
                    '* - You can’t remove the last member of a client.',
                    '* - You can’t delete a user if they are the last member of a team',
                    'Team Management - Team Preview is now available',
                    'Team Management - Removed the ability to edit locations  when editing a Client’s default team because that team can see all locations',
                    'Team Management - You can’t delete a client’s default team',
                    'Club Management - Club Preview is now available',
                    'Leads Management - Changed the formatting to Last Updated by verbiage making the timestamp more readable',
                    'Profile Management - New Fields - ',
                    '* - First Name',
                    '* - Last Name',
                    '* - Personal Email',
                    '* - Job Title',
                    '* - Address 1 & 2',
                    '* - City, State, & Zip',
                    'Admin Impersonation - GymRevenue and Account Owner users now have the ability to Impersonate a user.',
                    '* - Records are logged when this occurs',
                    '* - You cannot switch teams in this mode.',
                    '* - The toolbar turns red when in impersonation mode',
                    '* - The mode is available in the upper right hand menu dropdown with your name on it.',
                    'Some backend optimizations in the dummy data seeder'
                ]
            ]
        ]);
        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 03-02-2022',
            'value' => '2022-03-02',
            'misc' => [
                'buildno' => '20220302',
                'version' => '0.13.31',
                'notes' => [
                    'Mass Comm - You can now send yourself a test Text message of SMS Templates when inside of a GymRevenue team.',
                    'Mass Comm - You can now send yourself a test Email message of Email Templates when inside of a GymRevenue team.',
                    'User Mgnt - New Filters - ',
                    '* -- By Role',
                    '* -- By Team',
                    '* -- By Home Club',
                    'User Mgnt Create/Update - New Fields - ',
                    '* -- Home Club',
                    '* -- Start/End/Termination Dates',
                    '* -- Notes',
                    'Team Mgnt - New Filters - ',
                    '* -- Filter via which teams have a specific user assigned',
                    '* -- Filter via which teams have a specific club assigned',
                    'Lead Mgnt - New Filters - ',
                    '* -- Name',
                    '* -- Phone #',
                    '* -- Email',
                    '* -- Agreement',
                    '* -- Deleted',
                    '* -- Sort By Newest/Oldest',
                    '* -- By claimed Sales Rep',
                    '* -- Lead Type',
                    '* -- Lead Source',
                    '* -- Club',
                    '* -- Opportunity',
                    '* -- By DoB',
                    'Misc - Event Tracking Optimizations',
                    'Misc - Switch Team Dropdown is now scrollable',
                ]
            ]
        ]);
        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 03-09-2022',
            'value' => '2022-03-09',
            'misc' => [
                'buildno' => '20220309',
                'version' => '0.14.35',
                'notes' => [
                    'Users Mgnt - Can upload documentation connected to a User',
                    'Leads Mgnt - A Lead Preview is now available',
                    'Leads Mgnt - Added Opportunity-driven color-coded border around the Avatar in Edit and Sales Rep views',
                    'Leads Mgnt - Sales Rep view : Notes are included in the history',
                    'ToDo List - Setup a Calendar with pre-seeded dummy events',
                    'Misc - Backend Optimizations enhancing the note functionality',
                    'Misc - Backend pre-requisite code for the Calendar',
                    'Misc - State field in Users,Locations  and Profile Management CRUDs use a Dropdown instead of typing in the stateA',
                    'Misc - Added Sami’s last name to her account.'
                ]
            ]
        ]);

        AppState::firstOrCreate([
            'name' => 'Deployment Log Record',
            'slug' => 'deployment-log-record',
            'desc' => 'This is a record of code deployed on 04-06-2022',
            'value' => '2022-04-06',
            'misc' => [
                'buildno' => '20220406',
                'version' => '0.15.35',
                'notes' => [
                    'All List Views - Click to Preview, Double Click to Edit',
                    'All List Views - Customizable columns. No new columns at this time, but they are not able to be shown/hidden on a per user basis.',
                    'All List Views - Sortable Columns.  This does not work on all columns yet.',
                    'All List Views - Export and Export All buttons added that adapt to your column customization',
                    'Calendar - Attendees can now be added to an event. Invitations to come later.',
                    'Calendar - Sort by "My Events" or by a certain User\'s events. To be refined later.',
                    'Calendar - Theming / Color palettes hooked up.',
                    'Leads - List View - Opportunity Indicator added',
                    'Leads - Preview - Notes added',
                    'Edit Lead - Last Updated now correctly updates after an edit.',
                    'TruFit Clubs now added to seeder',
                    'Security Roles - Huge Refactor. Super solid and scalable now.',

                ]
            ]
        ]);
    }
}
