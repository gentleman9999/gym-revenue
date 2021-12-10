<?php

namespace Database\Seeders\GatewayProviders;

use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderDetail;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class GatewayProviderDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gateways = GatewayProvider::getAllProvidersAsArray();
        $details = [
            'twilio' => [
                [
                    'gateway_id' => $gateways['twilio']['id'],
                    'detail' => 'access_credential',
                    'value' => 'twilio_sid',
                    'misc' => ['required' => true],
                    'active' => 1
                ],
                [
                    'gateway_id' => $gateways['twilio']['id'],
                    'detail' => 'access_credential',
                    'value' => 'twilio_token',
                    'misc' => ['required' => true],
                    'active' => 1
                ],
                [
                    'gateway_id' => $gateways['twilio']['id'],
                    'detail' => 'access_credential',
                    'value' => 'twilio_no',
                    'misc' => ['required' => true],
                    'active' => 1
                ],
            ],
            'mailgun' => [
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'mailgun_domain',
                    'misc' => ['required' => true],
                    'active' => 1
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'mailgun_secret',
                    'misc' => ['required' => true],
                    'active' => 1
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'mailgun_endpoint',
                    'misc' => ['required' => true],
                    'active' => 1
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'default_mail_from_address',
                    'misc' => ['required' => false],
                    'active' => 1
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'default_mail_from_name',
                    'misc' => ['required' => false],
                    'active' => 1
                ]

            ],
        ];

        foreach($details as $slug => $detail_set)
        {
            VarDumper::dump("($slug)");
            foreach($detail_set as $idx => $detail)
            {
                VarDumper::dump("[{$idx}] ($slug) - {$detail['value']}");
                GatewayProviderDetail::firstOrCreate($detail);
            }
        }
    }
}
