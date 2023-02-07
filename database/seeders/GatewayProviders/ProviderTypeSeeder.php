<?php

namespace Database\Seeders\GatewayProviders;

use App\Models\GatewayProviders\GatewayProviderType;
use App\Support\Uuid;
use Illuminate\Database\Seeder;

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
            'chat' => 'Gateways that handles chats',
        ];

        $data = [];
        foreach ($types as $slug => $desc) {
            $data[] = [
                'id' => Uuid::get(),
                'name' => $slug,
                'desc' => $desc,
                'active' => 1,
            ];
        }

        GatewayProviderType::upsert($data, ['name']);
    }
}
