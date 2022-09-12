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
            'twilio-sms' => [
                [
                    'gateway_id' => $gateways['twilio-sms']['id'],
                    'detail' => 'access_credential',
                    'value' => 'twilio_sid',
                    'misc' => ['required' => true,'value' => 'AC6bad234db52cb4f7a8c466c92a8e8a50'],
                    'active' => 1,
                ],
                [
                    'gateway_id' => $gateways['twilio-sms']['id'],
                    'detail' => 'access_credential',
                    'value' => 'twilio_token',
                    'misc' => ['required' => true,'value' => '1531e87775390625d404a50bc0c15052'],
                    'active' => 1,
                ],
                [
                    'gateway_id' => $gateways['twilio-sms']['id'],
                    'detail' => 'access_credential',
                    'value' => 'twilio_no',
                    'misc' => ['required' => true,'value' => '+19562753856'],
                    'active' => 1,
                ],
            ],
            'mailgun' => [
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'mailgun_domain',
                    'misc' => ['required' => true,'value' => 'auto.theathleticclub.com'],
                    'active' => 1,
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'mailgun_secret',
                    'misc' => ['required' => true,'value' => '450a0212c7352bcc84d8feb5b05205d2-2b0eef4c-99b3599c'],
                    'active' => 1,
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'mailgun_endpoint',
                    'misc' => ['required' => true,'value' => 'api.mailgun.net'],
                    'active' => 1,
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'default_mail_from_address',
                    'misc' => ['required' => false, 'value' => 'automailer@mail.trufitathleticclubs.com'],
                    'active' => 1,
                ],
                [
                    'gateway_id' => $gateways['mailgun']['id'],
                    'detail' => 'access_credential',
                    'value' => 'default_mail_from_name',
                    'misc' => ['required' => false, 'value' => 'GymRevenue'],
                    'active' => 1,
                ],

            ],
        ];

        foreach ($details as $slug => $detail_set) {
            VarDumper::dump("($slug)");
            foreach ($detail_set as $idx => $detail) {
                VarDumper::dump("[{$idx}] ($slug) - {$detail['value']}");
                GatewayProviderDetail::firstOrCreate($detail);
            }
        }
    }
}
