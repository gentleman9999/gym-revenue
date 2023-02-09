<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

use App\Domain\AgreementTemplates\Actions\CreateAgreementTemplate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Projections\Contract;
use App\Enums\AgreementAvailabilityEnum;
use App\Services\Process;
use Illuminate\Database\Seeder;

class AgreementTemplatesSeeder extends Seeder
{
    public function run()
    {
        $amount_of_agreements = env('QUICK_SEED') ? 1 : 10;
        $agreement_availability_random = AgreementAvailabilityEnum::asArray(); // Get all the Clients
        $clients = Client::with('locations')->whereActive(1)->get()->toArray();
        $billing_schedule_id = BillingSchedule::first()->id;

        foreach ($clients as $i => $client) {
            // For each client, get all the locations
            if (! empty($client['locations'])) {
                self::createAgreementTemplate(
                    $amount_of_agreements,
                    $i === 0 ? 'Basic-Membership' : 'Basic-Personal_Training',
                    $client,
                    $agreement_availability_random,
                    $billing_schedule_id
                );
            }
        }
    }

    public static function createAgreementTemplate(
        int    $amount_of_agreements,
        string $contract_name,
        array  $client,
        array  $agreement_availability_random,
        string $billing_schedule_id
    ): void {
        $contract_id = Contract::whereClientId($client['id'])->whereName($contract_name)->first()->id;
        $client_json = json_encode($client);
        $name = "test contract for {$client['name']}";
        $process = Process::allocate(5);

        foreach ($client['locations'] as $location) {
            for ($x = 0; $x <= $amount_of_agreements; $x++) {
                $agreement_data['client_id'] = $client['id'];
                $agreement_data['gr_location_id'] = $location['gymrevenue_id'];
                $agreement_data['agreement_name'] = $name;
                $agreement_data['agreement_json'] = $client_json;
                $agreement_data['is_not_billable'] = rand(0, 1);
                $agreement_data['availability'] = $agreement_availability_random[array_rand($agreement_availability_random)];
                $agreement_data['billing_schedule_id'] = $billing_schedule_id;
                $agreement_data['contract_id'] = $contract_id;

                $process->queue([CreateAgreementTemplate::class, 'run'], $agreement_data);
            }
        }

        $process->run();
        echo("Generated Agreement Templates for {$client['name']}!\n");
    }
}
