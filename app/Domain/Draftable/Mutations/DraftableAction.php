<?php

declare(strict_types=1);

namespace App\Domain\Draftable\Mutations;

use App\Domain\Draftable\DraftableAggregate;
use App\Support\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;

/**
 * class CreateCalendarEvent
 * {
 *      use AsAction;
 *      use DraftableAction;
 *
 *      public function rules() {}
 *
 *      public function handleDraft(ActionRequest $request): RedirectResponse
 *      {
 *          $this->saveDraft(new CalendarEvent($request->validated()));
 *
 *          return Redirect::back();
 *      }
 *
 *      public function handle($data, ?User $user = null) {}
 *      public function getControllerMiddleware() {}
 *      public function authorize(ActionRequest $request) {}
 *      public function asController(ActionRequest $request) {}
 * }
 */
trait DraftableAction
{
    protected function isDraftCall(ActionRequest $request): bool
    {
        return in_array($request->query('is_draft'), ['true', '1', 'TRUE']);
    }

    protected function saveDraft(Model $model, Model $owner): void
    {
        DraftableAggregate::retrieve(Uuid::get())->create([
            'model' => $model,
            'owner' => $owner,
        ])->persist();
    }

    public function beforeAsController(ActionRequest $request, string $method, array $args): mixed
    {
        if ($this->isDraftCall($request)) {
            return $this->resolveAndCall('handleDraft');
        }

        return $this->{$method}(...$args);
    }

    public function getValidator(Factory $factory, ActionRequest $request): Validator
    {
        $rules = $this->resolveAndCall('rules');
        if ($this->isDraftCall($request)) {
            array_walk($rules, static function (array &$rule) {
                array_unshift($rule, 'nullable');
                $rule = array_unique(array_diff($rule, ['required']));
            });
        }

        return $factory->make(
            method_exists($this, 'getValidationData') ? $this->resolveAndCall('getValidationData') : $request->all(),
            method_exists($this, 'rules') ? $rules : [],
            method_exists($this, 'messages') ? $this->resolveAndCall('messages') : [],
            method_exists($this, 'attributes') ? $this->resolveAndCall('attributes') : [],
        );
    }

    protected function resolveAndCall(string $method): mixed
    {
        return app()->call([$this, $method]);
    }
}
