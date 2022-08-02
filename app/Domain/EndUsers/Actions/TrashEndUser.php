<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;

abstract class TrashEndUser extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => ['required','string'],
        ];
    }

    public function handle(EndUser $endUser, $reason)
    {
        ($this->getAggregate())::retrieve($endUser->id)->trash($reason)->persist();

        return $endUser->refresh();
    }
}
