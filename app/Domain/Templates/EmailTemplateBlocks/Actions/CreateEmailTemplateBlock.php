<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Actions;

use App\Domain\Templates\EmailTemplateBlocks\EmailTemplateBlockAggregates;
use App\Domain\Templates\EmailTemplateBlocks\Models\EmailTemplateBlock;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Ramsey\Uuid\Uuid;

class CreateEmailTemplateBlock
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
            'name' => ['required', 'max:255'],
            'definition' => ['required', 'array'],
        ];
    }

    public function handle($data): EmailTemplateBlock
    {
        $uuid = (string) Uuid::uuid4();
        EmailTemplateBlockAggregates::retrieve($uuid)->emailTemplateBlockCreated($data)->persist();

        return EmailTemplateBlock::find($uuid);
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('email-templates.create', EmailTemplate::class);
    }

    public function asController(ActionRequest $request): EmailTemplateBlock
    {
        return $this->handle($request->validated());
    }
}
