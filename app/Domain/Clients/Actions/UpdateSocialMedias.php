<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Models\Client;
use App\Http\Middleware\InjectClientId;
use App\Models\SocialMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateSocialMedias
{
    use AsAction;

    public function handle(array $payload): Client
    {
        if (array_key_exists('facebook', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('facebook')->first();
            if (is_null($social)) {
                SocialMedia::create([
                        'client_id' => $payload['client_id'],
                        'name' => 'facebook',
                        'value' => $payload['facebook'],
                    ]);
            } else {
                $social->update([
                        'client_id' => $payload['client_id'],
                        'name' => 'facebook',
                        'value' => $payload['facebook'],
                        ]);
            }
        } else {
        }
        if (array_key_exists('twitter', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('twitter')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'twitter',
                    'value' => $payload['twitter'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'twitter',
                    'value' => $payload['twitter'],
                ]);
            }
        }
        if (array_key_exists('instagram', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('instagram')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'instagram',
                    'value' => $payload['instagram'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'instagram',
                    'value' => $payload['instagram'],
                ]);
            }
        }
        if (array_key_exists('linkedin', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('linkedin')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'linkedin',
                    'value' => $payload['linkedin'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'linkedin',
                    'value' => $payload['linkedin'],
                ]);
            }
        }
        //ClientAggregate::retrieve($payload['client_id'])->setClientServices($payload['services'])->persist();

        return Client::findOrFail($payload['client_id']);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:255', 'exists:clients,id'],
            'facebook' => [ 'sometimes', 'string', 'nullable'],
            'twitter' => [ 'sometimes', 'string', 'nullable'],
            'instagram' => [ 'sometimes', 'string', 'nullable'],
            'linkedin' => [ 'sometimes', 'string', 'nullable'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

//        return $current_user->can('*');
        return $current_user->isAn('Admin', 'Account Owner');
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request): Client
    {
        $data = $request->validated();

        return $this->handle(
            $data,
        );
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Social Media '{$client->name}' services updated.")->flash();

        return Redirect::back();
    }
}
