<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserDetails;
use App\Domain\Users\PasswordValidationRules;
use App\Domain\Users\UserAggregate;
use App\Enums\StatesEnum;
use App\Http\Middleware\InjectClientId;
use App\Rules\Zip;

use function bcrypt;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

use function request;

class UpdateUser implements UpdatesUserProfileInformation
{
    use PasswordValidationRules;
    use AsAction;

    private bool $updatingSelf = false;

    public function handle(string $id, array $payload): User
    {
        if (array_key_exists('password', $payload)) {
            $payload['password'] = bcrypt($payload['password']);
        }
        $misc = (new UserDetails())->whereUserId($id)->whereField('emergency_contact')->first()?->misc ?: [];
        $new_ec = [
            'ec_first_name' => $payload['ec_first_name'] ?? ($misc['ec_first_name'] ?? null),
            'ec_last_name' => $payload['ec_last_name'] ?? ($misc['ec_last_name'] ?? null),
            'ec_phone' => $payload['ec_phone'] ?? ($misc['ec_phone'] ?? null),
        ];

        if (! empty(array_filter($new_ec))) {
            UserDetails::createOrUpdateRecord($id, 'emergency_contact', '', $new_ec, true);
        }

        UserAggregate::retrieve($id)->update($payload)->persist();

        if ($this->updatingSelf) {
            $user = User::withoutGlobalScopes()->findOrFail($id);
        } else {
            $user = User::findOrFail($id);
        }

        return $user;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . request()->id],
            'alternate_email' => ['sometimes', 'email'],
            'address1' => ['sometimes'],
            'address2' => ['sometimes', 'nullable'],
            'city' => ['sometimes'],
            'state' => ['sometimes', 'max:2', new Enum(StatesEnum::class)],
            'zip' => ['required', 'max:5', new Zip()],
            'start_date' => ['sometimes'],
            'end_date' => ['sometimes'],
            'termination_date' => ['sometimes'],
            'notes' => ['sometimes'],
            'team_id' => ['sometimes', 'string', 'exists:teams,id'],
            'role_id' => ['sometimes', 'integer'],
            'contact_preference' => ['sometimes', 'nullable'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['sometimes', 'digits:10'], //should be required, but seeders don't have phones.
//            'home_location_id' => ['sometimes', 'nullable', 'exists:locations,gymrevenue_id'], //should be required if client_id provided. how to do?
            'home_location_id' => ['sometimes', 'nullable'], //should be required if client_id provided. how to do?
            'departments' => ['sometimes', 'nullable'],
            'positions' => ['sometimes', 'nullable'],
            'ec_first_name' => ['sometimes', 'string', 'max:255'],
            'ec_last_name' => ['sometimes', 'string', 'max:255'],
            'ec_phone' => ['sometimes', 'digits:10'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.update', User::class);
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user->id,
            $request->validated(),
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("User '{$user->name}' was updated")->flash();

//        return Redirect::back();
        return Redirect::route('users.edit', $user->id);
    }

    /**
     * Validate and update the given user's profile information.
     *
     * @param mixed $user
     * @param array $input
     * @return void
     */
    public function update($user, array $input)
    {
        $this->updatingSelf = true;
        $input['client_id'] = $user->client_id;
        $input['id'] = $user->id;
        $this->handle($input['id'], $input);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'zip.required' => 'A Zip code is required',
            'zip.error' => 'Invalid Zip Code',
        ];
    }
}
