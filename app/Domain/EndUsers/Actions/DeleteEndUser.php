<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;

abstract class DeleteEndUser extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle(EndUser $lead, $current_user)
    {
        ($this->getAggregate())::retrieve($lead->id)->delete()->persist();

        return $lead;
    }
}
