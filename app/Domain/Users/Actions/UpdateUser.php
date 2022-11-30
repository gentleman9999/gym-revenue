<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\PasswordValidationRules;
use App\Domain\Users\UserAggregate;
use App\Enums\StatesEnum;
use App\Http\Middleware\InjectClientId;
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
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . request()->id],
            'alternate_email' => ['sometimes', 'required', 'email'],
            'address1' => ['sometimes', 'required'],
            'address2' => ['sometimes', 'nullable'],
            'city' => ['sometimes', 'required'],
            'state' => ['sometimes', 'required', 'max:2', new Enum(StatesEnum::class)],
            'zip' => ['sometimes', 'required'],
            'start_date' => ['sometimes'],
            'end_date' => ['sometimes'],
            'termination_date' => ['sometimes'],
            'notes' => ['sometimes'],
            'team_id' => ['sometimes', 'required', 'string', 'exists:teams,id'],
            'role_id' => ['sometimes', 'required', 'integer'],
            'contact_preference' => ['sometimes', 'nullable'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['sometimes', 'digits:10'], //should be required, but seeders don't have phones.
//            'home_location_id' => ['sometimes', 'nullable', 'exists:locations,gymrevenue_id'], //should be required if client_id provided. how to do?
            'home_location_id' => ['sometimes', 'nullable'], //should be required if client_id provided. how to do?
            'departments' => ['sometimes', 'nullable'],
            'positions' => ['sometimes', 'nullable'],
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
}
