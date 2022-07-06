<?php

namespace Database\Seeders;

use Database\Seeders\AccessControl\BouncerAbilitiesSeeder;
use Database\Seeders\AccessControl\CapeAndBayBouncerRolesSeeder;
use Database\Seeders\AccessControl\ClientBouncerRolesSeeder;
use Database\Seeders\Clients\ClientSeeder;
use Database\Seeders\Clients\EmailCampaignsSeeder;
use Database\Seeders\Clients\LocationSeeder;
use Database\Seeders\Clients\SecondaryTeamsSeeder;
use Database\Seeders\Clients\SMSCampaignsSeeder;
use Database\Seeders\Clients\TeamLocationsSeeder;
use Database\Seeders\Comm\EmailTemplateSeeder;
use Database\Seeders\Comm\SMSTemplateSeeder;
use Database\Seeders\Data\CalendarEventTypeSeeder;
use Database\Seeders\Data\CalendarSeeder;
use Database\Seeders\Data\LeadProspectSeeder;
use Database\Seeders\Data\LeadSourceSeeder;
use Database\Seeders\Data\LeadStatusSeeder;
use Database\Seeders\Data\LeadTypeSeeder;
use Database\Seeders\Data\MemberSeeder;
use Database\Seeders\Data\MembershipTypeSeeder;
use Database\Seeders\Data\TrialMembershipTypeSeeder;
use Database\Seeders\GatewayProviders\GatewayProviderDetailsSeeder;
use Database\Seeders\GatewayProviders\GatewayProviderSeeder;
use Database\Seeders\GatewayProviders\ProviderTypeSeeder;
use Database\Seeders\Users\CapeAndBayUserSeeder;
use Database\Seeders\Users\NewClientSeeder;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // This is where App State Records like Simulation Mode and Deployment Announcements Go
        VarDumper::dump('Setting the initial app state');
        $this->call(AppStateSeeder::class);

        // There are several types of Providers defined here
        VarDumper::dump('Creating Provider Types');
        $this->call(ProviderTypeSeeder::class);

        // Gateway Providers are the Services that do that the Provider Types are
        // example, sms, email, payment, crm, etc
        VarDumper::dump('Creating Gateways Providers');
        $this->call(GatewayProviderSeeder::class);

        // Details on variables required for code-level integrations are defined here
        VarDumper::dump('Adding Gateway Provider Details');
        $this->call(GatewayProviderDetailsSeeder::class);

        // This is where the admin role goes
        VarDumper::dump('Adding Bouncer Roles');
        $this->call(CapeAndBayBouncerRolesSeeder::class);

        // Cape & Bay / GymRevenue Users Go Here
        VarDumper::dump('Creating Cape & Bay Users');
        $this->call(CapeAndBayUserSeeder::class);

        // This Seeder here will onboard a Client entity including various other tasks like creating the default team
        VarDumper::dump('Running Client Seeder');
        $this->call(ClientSeeder::class);

        // This is where the client roles go
        VarDumper::dump('Adding Bouncer Roles');
        $this->call(ClientBouncerRolesSeeder::class);

        // This is where the abilities linked to the roles go
        VarDumper::dump('Adding Bouncer Abilities');
        $this->call(BouncerAbilitiesSeeder::class);

        VarDumper::dump('Adding Department and Positions w/ Syncing');
        $this->call(DepartmentSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(PositionDepartmentSync::class);

        // New clubs for clients are generated here
        VarDumper::dump('Running Client Location Seeder');
        $this->call(LocationSeeder::class);

        // Secondary Teams linked to each client's account owner are defined here.
        // There is a team for each location, along with various sales teams
        VarDumper::dump('Running Client Secondary Teams Seeder');
        $this->call(SecondaryTeamsSeeder::class);

        // Default Lead Types for each client are seeded here.
        VarDumper::dump('Running Lead Type Seeder');
        $this->call(LeadTypeSeeder::class);

        // Default Membership Types for each client are seeded here.
        VarDumper::dump('Running Membership Type Seeder');
        $this->call(MembershipTypeSeeder::class);

        // Default Trial Membership Types for each client are seeded here.
        VarDumper::dump('Trial Membership Type Seeder');
        $this->call(TrialMembershipTypeSeeder::class);

        // Default Lead Sources for each client are seeded here.
        VarDumper::dump('Running Lead Source Seeder');
        $this->call(LeadSourceSeeder::class);

        // Default Lead Statuses for each client are seeded here.
        VarDumper::dump('Running Lead Status Seeder');
        $this->call(LeadStatusSeeder::class);

        // This seeder assigns locations to teams
        VarDumper::dump('Running Client Team/Location Assignments Seeder');
        $this->call(TeamLocationsSeeder::class);

        // Regional Managers, Location Managers, Sales Reps and Employees are seeded here
        VarDumper::dump('Running Client Users Seeder');
        $this->call(NewClientSeeder::class);

        // This seeder generates dummy leads for each client
        VarDumper::dump('Running Leads Dummy Data Seeder');
        $this->call(LeadProspectSeeder::class);

        // This seeder generates dummy members for each client
        VarDumper::dump('Running Members Dummy Data Seeder');
        $this->call(MemberSeeder::class);

        // Baby's First Email Templates are Seeded for each client
        VarDumper::dump('Running Email Template  Seeder');
        $this->call(EmailTemplateSeeder::class);

        // Baby's First SMS Templates are Seeded for each client
        VarDumper::dump('Running SMS Template  Seeder');
        $this->call(SMSTemplateSeeder::class);

        // Email Campaigns are Seeded for each client
        VarDumper::dump('Running Email Campaign  Seeder');
        $this->call(EmailCampaignsSeeder::class);

        // SMS Campaigns are Seeded for each client
        VarDumper::dump('Running SMS Campaign  Seeder');
        $this->call(SMSCampaignsSeeder::class);

        // CalendarEventType Seeder
        VarDumper::dump('Running Calender Event Type Seeder');
        $this->call(CalendarEventTypeSeeder::class);

        // CalendarEvent Seeder
        if (env('SEED_CALENDAR_EVENTS', false) === true) {
            VarDumper::dump('Running Calender Event Seeder');
            $this->call(CalendarSeeder::class);
        }
    }
}
