<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Agreements\Projections\Agreement;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AgreementController extends Controller
{
    public function viewAgreementPDF(Request $request): Response
    {
        $is_signed    = false;
        $agreement_id = $request->agreement_id;
        $agreement    = Agreement::find($agreement_id);
        $user_name    = $agreement->user->name;
        $file         = File::whereFileableId($agreement_id)->whereType('signed')->first();

        if ($file) {
            $is_signed = true;
        } else {
            $file = File::whereFileableId($agreement_id)->whereType('unsigned')->first();
        }

        $file_name = $file->original_filename;
        $url       = Storage::disk('s3')->temporaryUrl($file->key, now()->addMinutes(10));

        return Inertia::render('Agreements/ViewPDF', [
            'pdfUrl' => $url,
            'agreementId' => $agreement_id,
            'userName' => $user_name,
            'fileName' => $file_name,
            'isSigned' => $is_signed,
        ]);
    }
}
