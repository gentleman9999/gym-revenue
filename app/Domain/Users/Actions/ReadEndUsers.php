<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\EndUser;

class ReadEndUsers extends BaseEndUserAction
{
    public const LOADABLE_RELATIONSHIPS = [
        'location',
        'membershipType',
        'claimed',
        'notes',
    ];

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|nullable',
        ];
    }

    public function handle(array $data, array $appendable): EndUser
    {
        if (array_key_exists('per_page', $data)) {
            $page_count = $data['per_page'] > 0 && $data['per_page'] < 1000 ? $data['per_page'] : 10;
        } else {
            $page_count = 10;
        }

        return $this->getModel()::with($this->getRelationshipsToLoad())
            ->orderBy('created_at', 'desc')
            ->paginate($page_count)
            ->appends($appendable);
    }

    public function asController(ActionRequest $request): EndUser
    {
        return $this->handle(
            $request->validated(),
            $request->except('page')
        );
    }

    protected function getRelationshipsToLoad(): array
    {
        return self::LOADABLE_RELATIONSHIPS;
    }
}
