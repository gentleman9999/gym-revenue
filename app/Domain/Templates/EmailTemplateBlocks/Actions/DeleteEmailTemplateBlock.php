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

class DeleteEmailTemplateBlock
{
    use AsAction;

    public function handle(EmailTemplateBlock $email_template_block): bool
    {
        EmailTemplateBlockAggregates::retrieve($email_template_block->id)->emailTemplateBlockDeleted()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('email-templates.create', EmailTemplate::class);
    }

    public function asController(EmailTemplateBlock $block): bool
    {
        return $this->handle($block);
    }

    public function jsonResponse(bool $status): JsonResponse
    {
        return new JsonResponse(['success' => $status]);
    }
}
