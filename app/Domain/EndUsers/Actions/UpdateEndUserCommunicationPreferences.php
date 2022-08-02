<?php

namespace App\Domain\EndUsers\Actions;

abstract class UpdateEndUserCommunicationPreferences extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'boolean'],
            'sms' => ['required', 'boolean'],
        ];
    }

    public function handle($id, $data)
    {
        ($this->getAggregate())::retrieve($id)->updateCommunicationPreferences($data['email'], $data['sms'])->persist();

        return ($this->getModel())::findOrFail($id);
    }
}
