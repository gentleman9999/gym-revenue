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
 *      // CREATE
 *      public function handleDraft(ActionRequest $request): RedirectResponse
 *      {
 *          $this->saveDraft(new DripCampaign($request->validated()), $request->user());
 *
 *          return Redirect::back();
 *      }
 *
 *      // UPDATE
 *      public function handleDraft(ActionRequest $request, string $draftable_id): RedirectResponse
 *      {
 *          $this->updateDraft($draftable_id, $request->validated());
 *          $this->deleteDraft($draftable_id);
 *
 *          return Redirect::back();
 *      }
 *
 *      // UPDATE (draft can also be deleted if 'draft.id' exist in a request without `?is_draft=1`
 *      public function handleDraft(ActionRequest $request, string $draftable_id): RedirectResponse
 *      {
 *          $this->deleteDraft($draftable_id);
 *
 *          return Redirect::back();
 *      }
 *
 *
 *      public function handle($data, ?User $user = null) {}
 *      public function getControllerMiddleware() {}
 *      public function authorize(ActionRequest $request) {}
 *      public function asController(ActionRequest $request) {}
 * }
 */
trait DraftableAction
{
    public function beforeAsController(ActionRequest $request): void
    {
        if ($request->has('draft.id')) {
            $this->deleteDraft($request->get('draft.id'));
        }
    }

    public function getValidator(Factory $factory, ActionRequest $request): Validator
    {
        $rules = $this->resolveAndCall('rules');
        if ($this->isDraftCall($request)) {
            array_walk($rules, static function (array &$rule): void {
                array_unshift($rule, 'nullable');
                $rule = array_unique(array_diff($rule, ['required']));
            });
        }

        return $factory->make(
            method_exists($this, 'getValidationData') ? $this->resolveAndCall('getValidationData') : $request->all(),
            (method_exists($this, 'rules') ? $rules : []) + ['draft.id' => ['sometimes', 'nullable']],
            method_exists($this, 'messages') ? $this->resolveAndCall('messages') : [],
            method_exists($this, 'attributes') ? $this->resolveAndCall('attributes') : [],
        );
    }

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

    protected function updateDraft(string $id, array $data): void
    {
        DraftableAggregate::retrieve($id)->update($data)->persist();
    }

    protected function deleteDraft(string $id): void
    {
        DraftableAggregate::retrieve($id)->delete()->persist();
    }

    /**
     * @param string $method
     *
     * @return array<mixed>
     */
    protected function resolveAndCall(string $method): array
    {
        return app()->call([$this, $method]);
    }
}
