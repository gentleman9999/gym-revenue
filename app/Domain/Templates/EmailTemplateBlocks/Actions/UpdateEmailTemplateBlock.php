<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Actions;

use App\Domain\Templates\EmailTemplateBlocks\EmailTemplateBlockAggregates;
use App\Domain\Templates\EmailTemplateBlocks\Models\EmailTemplateBlock;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEmailTemplateBlock
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
            'name' => ['string', 'required', 'max:255'],
            'definition' => ['array', 'required'],
        ];
    }

    public function handle(EmailTemplateBlock $email_template_block, array $data): bool
    {
        EmailTemplateBlockAggregates::retrieve($email_template_block->id)
            ->emailTemplateBlockUpdated($data)
            ->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('email-templates.update', EmailTemplate::class);
    }

    public function asController(ActionRequest $request, EmailTemplateBlock $block): bool
    {
        return $this->handle($block, $request->validated());
    }

    public function jsonResponse(bool $status): JsonResponse
    {
        return new JsonResponse(['success' => $status]);
    }
}
