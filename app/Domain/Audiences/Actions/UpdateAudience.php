<?php

namespace App\Domain\Audiences\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAudience
{
    use AsAction;

    public function handle(Audience $audience, array $payload): Audience
    {
        AudienceAggregate::retrieve($audience->id)->update($payload)->persist();

        return $audience->refresh();
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'filters' => ['sometimes', 'array', 'min:1'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
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
