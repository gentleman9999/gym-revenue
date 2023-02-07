<?php

namespace Database\Seeders\Data;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Contracts\ContractGates\Actions\CreateContractGate;
use App\Domain\Contracts\Projections\Contract;
use App\Domain\Locations\Projections\Location;
use App\Enums\ContractGateTypeEnum;
use Illuminate\Database\Seeder;

class ContractGatesSeeder extends Seeder
{
    public function run()
    {
        $cache = [];
        // Get all the Contracts
        foreach (Contract::all() as $contract) {
            echo("Generating Contract Gate for {$contract->name}!\n");

            $entities  = [];
            $client_id = $contract->client_id;

            if (isset($cache[$client_id])) {
                [$agreement_category, $location, $billing_schedule] = $cache[$client_id];
            } else {
                $agreement_category = AgreementCategory::whereClientId($client_id)->first();
                $location           = Location::whereClientId($client_id)->first();
                $billing_schedule   = BillingSchedule::whereClientId($client_id)->first();
                $cache[$client_id]  = [$agreement_category, $location, $billing_schedule];
            }

            if ($agreement_category) {
                $entities[] = [
                    'id'   => $agreement_category->id,
                    'type' => ContractGateTypeEnum::AgreementCategory,
                ];
            }

            if ($location) {
                $entities[] = [
                    'id'   => $location->gymrevenue_id,
                    'type' => ContractGateTypeEnum::Location,
                ];
            }

            if ($billing_schedule) {
                $entities[] = [
                    'id'   => $billing_schedule->id,
                    'type' => ContractGateTypeEnum::BillingSchedule,
                ];
            }

            if (empty($entities)) {
                continue;
            }

            $entity = $entities[array_rand($entities, 1)];

            $contract_gate_data['contract_id'] = $contract->id;
            $contract_gate_data['client_id']   = $client_id;
            $contract_gate_data['entity_id']   = $entity['id'];
            $contract_gate_data['entity_type'] = $entity['type'];
            CreateContractGate::run($contract_gate_data);
        }
    }
}
