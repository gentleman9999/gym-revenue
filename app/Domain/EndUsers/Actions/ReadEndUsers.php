<?php

namespace App\Domain\EndUsers\Actions;

use function request;

class ReadEndUsers extends BaseEndUserAction
{
    protected function getRelationshipsToLoad(): array
    {
        return [
            'location',
            'membershipType',
            'claimed',
            'notes',
        ];
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'per_page' => 'sometimes|nullable',
        ];
    }

    public function handle(array $data)
    {
        if (array_key_exists('per_page', $data)) {
            $page_count = $data['per_page'] > 0 && $data['per_page'] < 1000 ? $data['per_page'] : 10;
        } else {
            $page_count = 10;
        }

        return ($this->getModel())::with($this->getRelationshipsToLoad())
            ->orderBy('created_at', 'desc')
            ->paginate($page_count)
            ->appends(request()->except('page'));
    }
}
