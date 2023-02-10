<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Actions;

use App\Domain\Templates\EmailTemplateBlocks\Models\EmailTemplateBlock;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetEmailTemplateBlock
{
    use AsAction;

    /**
     *
     */
    public function handle(string $user_id): Collection
    {
        return EmailTemplateBlock::where(['user_id' => $user_id])->get();
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

    public function asController(ActionRequest $request): Collection
    {
        return $this->handle($request->user()->id);
    }

    public function jsonResponse(Collection $result): JsonResponse
    {
        return new JsonResponse(['blocks' => $result->toArray()]);
    }
}
