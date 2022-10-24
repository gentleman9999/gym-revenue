<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Actions;

use App\Actions\Clients\Files\CreateFile;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;

class UploadFile extends CreateFile
{
    public function asController(ActionRequest $request)
    {
        return $this->handle($request->validated(), $request->user());
    }

    public function jsonResponse(File $file): JsonResponse
    {
        return new JsonResponse($file);
    }
}
