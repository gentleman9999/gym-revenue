<?php

namespace Database\Seeders;

use Database\Seeders\AccessControl\BouncerAbilitiesSeeder;
use Database\Seeders\AccessControl\CapeAndBayBouncerRolesSeeder;
use Database\Seeders\AccessControl\ClientBouncerRolesSeeder;
use Database\Seeders\Clients\ClientSeeder;
use Database\Seeders\Clients\GymAmenitySeeder;
use Database\Seeders\Clients\LocationSeeder;
use Database\Seeders\Clients\LocationVendorCategorySeeder;
use Database\Seeders\Clients\LocationVendorSeeder;
use Database\Seeders\Clients\SecondaryTeamsSeeder;
use Database\Seeders\Clients\TeamLocationsSeeder;
use Database\Seeders\Comm\CallScriptTemplateSeeder;
use Database\Seeders\Comm\DripCampaignSeeder;
use Database\Seeders\Comm\EmailTemplateSeeder;
use Database\Seeders\Comm\ScheduledCampaignSeeder;
use Database\Seeders\Comm\SMSTemplateSeeder;
use Database\Seeders\Contract\ClientContractSeeder;
use Database\Seeders\Data\AgreementsSeeder;
use Database\Seeders\Data\AgreementTemplatesSeeder;
use Database\Seeders\Data\BillingScheduleSeeder;
use Database\Seeders\Data\CalendarEventSeeder;
use Database\Seeders\Data\CalendarEventTypeSeeder;
use Database\Seeders\Data\ContractGatesSeeder;
use Database\Seeders\Data\EndUserSeeder;
use Database\Seeders\Data\EntrySourceCategorySeeder;
use Database\Seeders\Data\EntrySourceSeeder;
use Database\Seeders\Data\LeadStatusSeeder;
use Database\Seeders\Data\LeadTypeSeeder;
use Database\Seeders\Data\NicknameSeeder;
use Database\Seeders\GatewayProviders\GatewayProviderDetailsSeeder;
use Database\Seeders\GatewayProviders\GatewayProviderSeeder;
use Database\Seeders\GatewayProviders\ProviderTypeSeeder;
use Database\Seeders\Users\CapeAndBayUserSeeder;
use Database\Seeders\Users\ClientUserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $time = microtime(true);
        Cache::put('is_seeding', true);
        // This is where App State Records like Simulation Mode and Deployment Announcements Go
        echo "Setting the initial app state\n";
        $this->call(AppStateSeeder::class);


        // There are several types of Providers defined here
        echo "Creating Provider Types\n";
        $this->call(ProviderTypeSeeder::class);

        // Gateway Providers are the Services that do that the Provider Types are
        // example, sms, email, payment, crm, etc
        echo "Creating Gateways Providers\n";
        $this->call(GatewayProviderSeeder::class);


        // Details on variables required for code-level integrations are defined here
        echo "Adding Gateway Provider Details\n";
        $this->call(GatewayProviderDetailsSeeder::class);

        // This is where the admin role goes
        echo "Adding Bouncer Roles\n";
        $this->call(CapeAndBayBouncerRolesSeeder::class);

        // Cape & Bay / GymRevenue Users Go Here
        echo "Creating Cape & Bay Users\n";
        $this->call(CapeAndBayUserSeeder::class);

        // This Seeder here will onboard a Client entity including various other tasks like creating the default team
        echo "Running Client Seeder\n";
        $this->call(ClientSeeder::class);

        // This is where the client roles go
        echo "Adding Bouncer Roles\n";
        $this->call(ClientBouncerRolesSeeder::class);

        // This is where the abilities linked to the roles go
        echo "Adding Bouncer Abilities\n";
        $this->call(BouncerAbilitiesSeeder::class);


        echo "Adding Department and Positions w/ Syncing\n";
        $this->call(DepartmentSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(PositionDepartmentSync::class);

        // New clubs for clients are generated here
        echo "Running Client Location Seeder\n";
        $this->call(LocationSeeder::class);

        // Create Location Vendor categories
        echo "Running Location Vendor Category Seeder\n";
        $this->call(LocationVendorCategorySeeder::class);

        // New vendors for different location are created here
        echo "Running Location Vendor Seeder\n";
        $this->call(LocationVendorSeeder::class);


        // New Gym Amenities for different location are created here
        echo "Running Location Vendor Seeder\n";
        $this->call(GymAmenitySeeder::class);

        // Secondary Teams linked to each client's account owner are defined here.
        // There is a team for each location, along with various sales teams
        echo "Running Client Secondary Teams Seeder\n";
        $this->call(SecondaryTeamsSeeder::class);

        // Default Lead Types for each client are seeded here.
        echo "Running Lead Type Seeder\n";
        $this->call(LeadTypeSeeder::class);

//        // Default Membership Types for each client are seeded here.
//        VarDumper::dump('Running Membership Type Seeder');
//        $this->call(MembershipTypeSeeder::class);
//
//        // Default Trial Membership Types for each client are seeded here.
//        echo "Trial Membership Type Seeder\n";
//        $this->call(TrialMembershipTypeSeeder::class);

        // Default Entry Source Categories for each client are seeded here.
        echo "Running Entry Source Category Seeder\n";
        $this->call(EntrySourceCategorySeeder::class);

        // Default Entry Sources for each client are seeded here.
        echo "Running Entry Source Seeder\n";
        $this->call(EntrySourceSeeder::class);
//
//        // Default Lead Statuses for each client are seeded here.
//        echo "Running Lead Status Seeder\n";
//        $this->call(LeadStatusSeeder::class);

        // This seeder assigns locations to teams
        echo "Running Client Team/Location Assignments Seeder\n";
        $this->call(TeamLocationsSeeder::class);


        // Regional Managers, Location Managers, Sales Reps and Employees are seeded here
        echo "Running Client Users Seeder\n";
        $this->call(ClientUserSeeder::class);


        // This seeder generates dummy End Users for each client
        echo "Running End Users Dummy Data Seeder\n";
        $this->call(EndUserSeeder::class);

        echo "Running Billing Schedule Data Seeder\n";
        $this->call(BillingScheduleSeeder::class);


        echo "Running Client Contract PDF Data Seeder\n";
        $this->call(ClientContractSeeder::class);

        echo "Running Agreement Templates Data Seeder\n";
        $this->call(AgreementTemplatesSeeder::class);

        echo "Running Agreements Data Seeder\n";
        $this->call(AgreementsSeeder::class);

        // This seeder generates dummy members for each client
        echo "Running Contract Gate Dummy Data Seeder\n";
        $this->call(ContractGatesSeeder::class);

        // Baby's First Email Templates are Seeded for each client
        echo "Running Email Template  Seeder\n";
        $this->call(EmailTemplateSeeder::class);

        // Baby's First SMS Templates are Seeded for each client
        echo "Running SMS Template  Seeder\n";
        $this->call(SMSTemplateSeeder::class);


        // Baby's First SMS Templates are Seeded for each client
        echo "Running Call Script Template  Seeder\n";
        $this->call(CallScriptTemplateSeeder::class);


        // CalendarEventType Seeder
        echo "Running Calender Event Type Seeder\n";
        $this->call(CalendarEventTypeSeeder::class);

        // CalendarEvent Seeder
        if (env('SEED_CALENDAR_EVENTS', false) === true) {
            echo "Running Calender Event Seeder\n";
            $this->call(CalendarEventSeeder::class);
        }

        if (env('SEED_CALENDAR_EVENTS', false) === true) {
            echo "Running Scheduled Campaign Seeder\n";
            $this->call(ScheduledCampaignSeeder::class);
        }

        if (env('SEED_CALENDAR_EVENTS', false) === true) {
            echo "Running Drip Campaign Seeder\n";
            $this->call(DripCampaignSeeder::class);
        }

        // Nickname Seeder
        echo "Nickname Seeder\n";
        $this->call(NicknameSeeder::class);

        Cache::put('is_seeding', false);

        dump(microtime(true) - $time);
    }
}
