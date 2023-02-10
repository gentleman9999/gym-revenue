<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

class ShortUrlController extends Controller
{
    public function index(string $external_url): RedirectResponse
    {
        return redirect(ShortUrl::whereExternalUrl($external_url)->firstOrFail()->route);
    }
}
