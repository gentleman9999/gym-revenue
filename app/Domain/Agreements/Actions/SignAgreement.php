<?php

declare(strict_types=1);

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SignAgreement
{
    use AsAction;

    public function rules(): array
    {
        return [
            'pdfUrl' => ['string','sometimes'],
            'signatureFile' => ['string','sometimes'],
            'fileName' => ['string','sometimes'],
            'active' => ['bool','sometimes'],
            'user_id' => ['integer','sometimes'],
        ];
    }

    public function handle(array $params, string $agreement_id): string
    {
        $url = '';
        AgreementAggregate::retrieve($agreement_id)->sign($params)->persist();

        $file = File::whereFileableId($agreement_id)->whereType('signed')->first();
        if ($file) {
            $url = Storage::disk('s3')->temporaryUrl($file->key, now()->addMinutes(10));
        }

        return $url;
    }

    public function asController(ActionRequest $request, string $agreement_id): string
    {
        $params            = $request->validated();
        $agreement         = Agreement::find($agreement_id);
        $params['user_id'] = $agreement->user->id;
        $params['active']  = $agreement->active;

        return $this->handle($params, $agreement_id);
    }

    public function jsonResponse(string $url): jsonResponse
    {
        return new JsonResponse(['url' => $url]);
    }
}
