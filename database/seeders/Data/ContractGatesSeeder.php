<?php

namespace Database\Seeders\Data;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Contracts\ContractGates\Actions\CreateContractGate;
use App\Domain\Contracts\Projections\Contract;
use App\Domain\Locations\Projections\Location;
use App\Enums\ContractGateTypeEnum;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ContractGatesSeeder extends Seeder
{
    public function run()
    {
        VarDumper::dump('Getting Contracts');
        // Get all the Contracts
        $contracts = Contract::all();
        if (count($contracts) > 0) {
            foreach ($contracts as $contract) {
                VarDumper::dump('Generating Contract Gate for ' . $contract->name . '!');

                $agreement_category = AgreementCategory::whereClientId($contract->client_id)->first();
                $location = Location::whereClientId($contract->client_id)->first();
                $billing_schedule = BillingSchedule::whereClientId($contract->client_id)->first();

                $entities = [];

                if ($agreement_category) {
                    $entities[] = [
                        'id' => $agreement_category->id,
                        'type' => ContractGateTypeEnum::AgreementCategory,
                    ];
                }

                if ($location) {
                    $entities[] = [
                        'id' => $location->gymrevenue_id,
                        'type' => ContractGateTypeEnum::Location,
                    ];
                }

                if ($billing_schedule) {
                    $entities[] = [
                        'id' => $billing_schedule->id,
                        'type' => ContractGateTypeEnum::BillingSchedule,
                    ];
                }

                if (empty($entities)) {
                    continue;
                }

                $entity = $entities[array_rand($entities, 1)];

                $contract_gate_data['contract_id'] = $contract->id;
                $contract_gate_data['client_id'] = $contract->client_id;
                $contract_gate_data['entity_id'] = $entity['id'];
                $contract_gate_data['entity_type'] = $entity['type'];
                CreateContractGate::run($contract_gate_data);
            }
        }
    }
}
