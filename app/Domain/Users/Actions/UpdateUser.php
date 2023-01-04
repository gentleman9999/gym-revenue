<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserDetails;
use App\Domain\Users\PasswordValidationRules;
use App\Domain\Users\ValidationRules;
use App\Enums\UserTypesEnum;
use App\Http\Middleware\InjectClientId;

use function bcrypt;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateUser implements UpdatesUserProfileInformation
{
    use PasswordValidationRules;
    use AsAction;

    private bool $updatingSelf = false;

    public function handle(User $user, array $payload): User
    {
        $previous_type = $this->getPreviousUserType((string)$user->id);
        if (array_key_exists('password', $payload)) {
            $payload['password'] = bcrypt($payload['password']);
        }
        $misc = (new UserDetails())->whereUserId($user->id)->whereField('emergency_contact')->first()?->misc ?: [];
        $new_ec = [
            'ec_first_name' => $payload['ec_first_name'] ?? ($misc['ec_first_name'] ?? null),
            'ec_last_name' => $payload['ec_last_name'] ?? ($misc['ec_last_name'] ?? null),
            'ec_phone' => $payload['ec_phone'] ?? ($misc['ec_phone'] ?? null),
        ];

        if (! empty(array_filter($new_ec))) {
            UserDetails::createOrUpdateRecord((string)$user->id, 'emergency_contact', '', $new_ec, true);
        }

        UserAggregate::retrieve((string)$user->id)->update($payload)->persist();

        if ($this->updatingSelf) {
            $user = User::withoutGlobalScopes()->findOrFail($user->id);
        } else {
            $user = User::findOrFail($user->id);
        }

        ReflectUserData::run($user, $previous_type);

        return $user;
    }

    /**
     * Custom validation based on user_type
     *
     * @return array
     */
    public function rules(): array
    {
        return ValidationRules::getValidationRules(request()->user_type, false);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    /**
     * Transform the request object in
     * preparation for validation
     *
     * @param ActionRequest $request
     */
    public function prepareForValidation(ActionRequest $request): void
    {
        $request = $this->mergeUserTypeToRequest($request);

        if ($request->user_type == UserTypesEnum::LEAD) {
            /** @TODO: Need to update with what entry_source data should be */
            $request->merge(['entry_source' => json_encode(['id' => 'some id', 'metadata' => ['something' => 'yes', 'something_else' => 'also yes']])]);
        }
    }

    /**
     * Adds the user type to the request object
     * based on the route name of the request
     *
     * @param ActionRequest $request
     *
     * @return ActionRequest $request
     */
    private function mergeUserTypeToRequest(ActionRequest $request): ActionRequest
    {
        $current_route = Route::currentRouteName();

        if ($current_route == 'users.store' || $current_route == 'users.update') {
            $request->merge(['user_type' => UserTypesEnum::EMPLOYEE]);
        } elseif ($current_route == 'data.leads.store' || $current_route == 'data.leads.update') {
            $request->merge(['user_type' => UserTypesEnum::LEAD]);
        } elseif ($current_route == 'data.members.store' || $current_route == 'data.members.update') {
            $request->merge(['user_type' => UserTypesEnum::MEMBER]);
        } else {
            $request->merge(['user_type' => UserTypesEnum::CUSTOMER]);
        }

        return $request;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $request->user_type == UserTypesEnum::EMPLOYEE ?
            $current_user->can('users.update', User::class) :
            $current_user->can('endusers.update', EndUser::class);
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user,
            $request->validated(),
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        $type = $user->user_type == UserTypesEnum::EMPLOYEE ? 'User' : ucwords($user->user_type->value);
        Alert::success("{$type} '{$user->name}' was updated.")->flash();

        return $user->user_type == UserTypesEnum::EMPLOYEE ?
            Redirect::route('users.edit', $user->id) :
            Redirect::back();
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
        $this->handle($user, $input);
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

    protected function getPreviousUserType(string $id): UserTypesEnum
    {
        if ($this->updatingSelf) {
            $user = User::withoutGlobalScopes()->findOrFail($id);
        } else {
            $user = User::findOrFail($id);
        }

        return $user->user_type;
    }
}
