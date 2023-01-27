<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Actions\GymRevAction;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Domain\Users\PasswordValidationRules;
use App\Domain\Users\ValidationRules;
use App\Enums\UserTypesEnum;

use function bcrypt;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateUser extends GymRevAction implements UpdatesUserProfileInformation
{
    use PasswordValidationRules;

    private bool $updatingSelf = false;

    public function handle(User $user, array $payload): User
    {
        dd($payload);
        $payload['user_type'] = $payload['user_type'] ?? $user->user_type;
        if (array_key_exists('password', $payload)) {
            $payload['password'] = bcrypt($payload['password']);
        }

        UserAggregate::retrieve((string)$user->id)->update($payload)->persist();

        if ($this->updatingSelf) {
            $user = User::withoutGlobalScopes()->findOrFail($user->id);
        } else {
            $user = User::findOrFail($user->id);
        }

        return $user;
    }

    public function mapArgsToHandle(array $args): array
    {
        $user = $args['input'];

        return [User::find($user['id']), $user];
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

    /**
     * Validate the address provided using USPS API after main rules
     * Which also sends back correct address1, city and state
     *
     * @return void
     */
    public function afterValidator(Validator $validator, ActionRequest $request): void
    {
        /**
         * @TODO: Send the suggestion data back to UI, and display to the User.
         * They can make a choice (confirm/cancel), and have it update if confirmed
         */
        session()->forget('address_validation');
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
        $route = 'data.customers';

        if ($user->user_type == UserTypesEnum::EMPLOYEE) {
            $route = 'users';
        } elseif ($user->user_type == UserTypesEnum::LEAD) {
            $route = 'data.leads';
        } elseif ($user->user_type == UserTypesEnum::MEMBER) {
            $route = 'data.members';
        }

        return Redirect::route($route);
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

    public function getValidationAttributes(): array
    {
        return [
            'addres1' => 'address line 1',
        ];
    }
}
