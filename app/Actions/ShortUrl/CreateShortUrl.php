<?php

declare(strict_types=1);

namespace App\Actions\ShortUrl;

use App\Aggregates\Clients\ShortUrlAggregate;
use App\Models\ShortUrl;
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
    public function rules(): array
    {
        return [
            'route' => 'string|required',
        ];
    }

    public function handle($data, $client_id)
    {
        $data['external_url'] = Str::random(10);
        $short_url            = ShortUrlAggregate::retrieve($client_id)
            ->createShortUrl("Auto Generated", $data)
            ->persist();

        return ShortUrl::latest()->first();
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle(
            $request->validated(),
            $request->user(),
        );
    }
}
