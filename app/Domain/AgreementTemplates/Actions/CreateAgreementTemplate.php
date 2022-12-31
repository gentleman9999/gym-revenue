<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Enums\AgreementAvailabilityEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreementTemplate
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
            'billing_schedule' => ['required, exists:agreement_template_billing_schedule'],
            'agreement_json' => ['required','json'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'is_not_billable' => ['sometimes', 'boolean'],
            'availability' => ['required', 'string', new Enum(AgreementAvailabilityEnum::class)],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function handle(array $data): AgreementTemplate
    {
        $id = Uuid::get();
        AgreementTemplateAggregate::retrieve($id)->create($data)->persist();

        return AgreementTemplate::findOrFail($id);
    }

    public function asController(ActionRequest $request): AgreementTemplate
    {
        return ($this->handle($request->validated()));
    }
}
