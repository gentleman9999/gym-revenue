<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AgreementController extends Controller
{
    public function viewAgreementPDF(Request $request): Response
    {
        $agreement_id = $request->agreement_id;
        $file = File::whereFileableId($agreement_id)->first();
        $url = Storage::disk('s3')->temporaryUrl($file->key, now()->addMinutes(10));

        return Inertia::render('Agreements/ViewPDF', [
            'pdfUrl' => $url,
            'agreementId' => $agreement_id,
        ]);
    }
}
