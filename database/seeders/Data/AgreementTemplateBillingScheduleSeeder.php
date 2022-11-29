<?php

namespace Database\Seeders\Data;

use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use Illuminate\Database\Seeder;

class AgreementTemplateBillingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agreement_templates = AgreementTemplate::all();
        foreach ($agreement_templates as $agreement_template) {
            $bs = BillingSchedule::whereClientId($agreement_template['client_id'])->whereType('Paid In Full')->first();
            $bs2 = BillingSchedule::whereClientId($agreement_template['client_id'])->whereType('Term')->first();

            $agreement_template->billingSchedule()->sync([$agreement_template['id'],$bs->id, $bs2->id]);
        }
    }
}
