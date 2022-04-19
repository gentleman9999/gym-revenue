<?php

namespace App\Actions\ShortUrl;

use App\Aggregates\Clients\ShortUrlAggregate;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateShortUrl
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'route' => 'string|required',
        ];
    }

    public function handle($data, $client_id)
    {
        $data['external_url'] = Str::random(10);
        ShortUrlAggregate::retrieve($client_id)
            ->createShortUrl( "Auto Generated", $data)
            ->persist();

        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $this->handle(
            $request->validated(),
            $request->user(),
        );

    }
}
