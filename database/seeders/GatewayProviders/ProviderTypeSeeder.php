<?php

namespace Database\Seeders\GatewayProviders;

use App\Models\GatewayProviders\GatewayProviderType;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ProviderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'sms' => 'Gateways that Transmit SMS/Text Messages',
            'voice' => 'Gateways that handles inbound/outbound voice calls',
            'email' => 'Gateways that Send Emails',
            'credit' => 'Gateways that Accept Credit Cards',
            'checking' => 'Gateways that Accept Checks',
            'alt-payment' => 'Gateways that accept alternate payment methods',
            'crm' => 'Gateways that manage EndUser and Employee Resources',
            'prospect' => 'Gateways that manage Prospects and Leads',
            'analytics' => 'Gateways that Manage Trackers and Resource Data',
        ];

        foreach ($types as $slug => $desc) {
            VarDumper::dump("($slug) - {$desc}");
            GatewayProviderType::firstOrCreate([
                'name' => $slug,
                'desc' => $desc,
                'active' => 1,
            ]);
        }
    }
}
