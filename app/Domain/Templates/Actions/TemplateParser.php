<?php

declare(strict_types=1);

namespace App\Domain\Templates\Actions;

use App\Services\TemplateParserService;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TemplateParser
{
    use AsAction;

    public function asController(ActionRequest $request): array
    {
        return TemplateParserService::CONSTRAINTS_NAMES;
    }

    public function jsonResponse(array $constraints): JsonResponse
    {
        return new JsonResponse($constraints);
    }
}
