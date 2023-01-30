<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Actions;

use App\Domain\Files\Actions\CreateFile;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;

class UploadFile extends CreateFile
{
    public function asController(ActionRequest $request)
    {
        $model = EmailTemplate::find($request->email_template_id);

        return $this->handle($request->validated(), $model, $request->user());
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function jsonResponse(File $file): JsonResponse
    {
        return new JsonResponse($file);
    }
}
