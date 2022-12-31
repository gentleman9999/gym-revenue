<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;

class UpdateEndUserCommunicationPreferences extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'boolean'],
            'sms' => ['required', 'boolean'],
        ];
    }

    public function handle($id, $data): EndUser
    {
        $end_user = EndUser::findOrFail($id);

        if ($end_user) {
            UserAggregate::retrieve($id)->updateCommunicationPreferences($data['email'], $data['sms'])->persist();

            return EndUser::findOrFail($id);
        }

        return $end_user;
    }
}
