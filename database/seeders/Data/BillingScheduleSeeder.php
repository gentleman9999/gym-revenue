<?php

namespace Database\Seeders\Data;

use App\Domain\BillingSchedules\Actions\CreateBillingSchedule;
use App\Domain\Clients\Projections\Client;
use App\Enums\BillingScheduleTypesEnum;
use Illuminate\Database\Seeder;

class BillingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amount_of_billing_schedules = 10;
        if (env('QUICK_SEED')) {
            $amount_of_billing_schedules = 2;
        }
        $clients = Client::whereActive(1)->get();
        $billing_types = [0 => BillingScheduleTypesEnum::PAID_IN_FULL, 1 => BillingScheduleTypesEnum::TERM];
        foreach ($clients as $client) {
            for ($i = 1; $i <= count($billing_types); $i++) {
                $billing_type = $billing_types[$i - 1]->value;
                if ($billing_type == BillingScheduleTypesEnum::PAID_IN_FULL) {
                    $is_open_ended = false;
                    $is_renewable = false;
                    $should_renew_automatically = false;
                    $term_length = 0;
                    $min_terms = 0;
                    $amount = rand(10, 999) . "." .rand(0, 99);
                } else {
                    $is_open_ended = rand(0, 1);
                    $is_renewable = rand(0, 1);
                    $should_renew_automatically = rand(0, 1);
                    $term_length = rand(0, 12);
                    $min_terms = rand(0, $term_length);
                    $amount = rand(1, 99) . "." .rand(0, 99);
                }
                $billing_schedule['client_id'] = $client['id'];
                $billing_schedule['type'] = $billing_type;
                $billing_schedule['is_open_ended'] = $is_open_ended;
                $billing_schedule['is_renewable'] = $is_renewable;
                $billing_schedule['should_renew_automatically'] = $should_renew_automatically;
                $billing_schedule['term_length'] = $term_length;
                $billing_schedule['min_terms'] = $min_terms;
                $billing_schedule['amount'] = $amount;
                CreateBillingSchedule::run($billing_schedule);
            }
        }
    }
}
