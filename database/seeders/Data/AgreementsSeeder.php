<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\Agreements\Actions\SignAgreement;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Projections\Contract;
use App\Models\File;
use App\Services\Process;
use Illuminate\Database\Seeder;

class AgreementsSeeder extends Seeder
{
    public function run(): void
    {
        $amount_of_agreements = 10;
        if (env('QUICK_SEED')) {
            $amount_of_agreements = 2;
        }
        // Get all the Clients
        $clients = Client::with(
            'employees',
            'locations',
            'members',
            'customers',
            'agreementCategories',
        )->whereActive(1)->get();
        $process = Process::allocate(5);

        foreach ($clients as $client) {
            $employee_ids     = $client->employees->pluck('id')->toArray();
            $categories       = $client->agreementCategories->toArray();
            $file             = File::whereClientId($client->id)->whereFileableType(Contract::class)->first();
            $amount_of_user   = count($employee_ids);
            $agreement_tpl_id = AgreementTemplate::whereClientId($client->id)->first()->id;

            //Make half of the users members or customers.
            if ($amount_of_agreements > $amount_of_user) {
                $amount_of_agreements = ceil($amount_of_user / 2);
            } else {
                $amount_of_agreements = ceil($amount_of_agreements / 2);
            }

            $lead_ids = $client->leads()
                ->whereJsonContains('details->membership_type_id', '')
                ->get()
                ->pluck('id')
                ->toArray();

            // For each client, get all the locations
            foreach ($client->locations as $location) {
                for ($x = 0; $x <= $amount_of_agreements; $x++) {
                    $end_user_id                             = $lead_ids[array_rand($lead_ids)];
                    $agreement_data['client_id']             = $client->id;
                    $agreement_data['agreement_category_id'] = $categories[array_rand($categories)]['id'];
                    $agreement_data['gr_location_id']        = $location->gymrevenue_id;
                    $agreement_data['created_by']            = $employee_ids[array_rand($employee_ids)];
                    $agreement_data['user_id']               = $end_user_id;
                    $agreement_data['agreement_template_id'] = $agreement_tpl_id;
                    $agreement_data['active']                = true;
                    if ($file !== null) {
                        $agreement_data['contract_file_id'] = $file->id;
                    }

                    $process->queue(
                        [self::class, 'createAndSignAgreement'],
                        $agreement_data,
                        $end_user_id,
                        "Generated/Signined Agreements for {$client->name}!\n",
                    );
                }
            }

            $process->onResolve(static function ($_, string $message): void {
                echo $message;
            });
        }

        $process->run();
    }

    public static function createAndSignAgreement(array $agreement_data, string $end_user_id, string $message)
    {
        $sign_agreement_data['user_id'] = $end_user_id;
        $sign_agreement_data['active']  = true;
        SignAgreement::run($sign_agreement_data, CreateAgreement::run($agreement_data)->id);

        return $message;
    }
}
