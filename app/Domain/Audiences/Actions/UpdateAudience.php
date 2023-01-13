<?php

namespace App\Domain\Audiences\Actions;

use App\Actions\GymRevAction;
use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use Lorisleiva\Actions\ActionRequest;

class UpdateAudience extends GymRevAction
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'filters' => ['sometimes', 'array', 'min:1'],
        ];
    }

    public function handle(Audience $audience, array $data): Audience
    {
        AudienceAggregate::retrieve($audience->id)->update($data)->persist();

        return $audience->refresh();
    }

    public function mapArgsToHandle($args): array
    {
        $audience = $args['input'];

        return [Audience::find($audience['id']), $audience];
    }

    public function asController(ActionRequest $request, Audience $audience): Audience
    {
        $data = $request->validated();

        return $this->handle(
            $audience,
            $data
        );
    }
}
