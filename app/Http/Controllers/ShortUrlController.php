<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;

class ShortUrlController extends Controller
{
    public function index($external_url)
    {
        $urlData = ShortUrl::whereExternalUrl($external_url)->firstOrFail();

        return redirect($urlData->route);
    }
}
