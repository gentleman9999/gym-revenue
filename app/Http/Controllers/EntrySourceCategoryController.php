<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\EntrySourceCategories\EntrySourceCategory;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EntrySourceCategoryController extends Controller
{
    public function index(): InertiaResponse
    {
        $esc = EntrySourceCategory::all();

        return Inertia::render('EntrySourceCategories/Index', [
            'entrySourceCategories' => $esc,
        ]);
    }

    public function edit(EntrySourceCategory $entrySourceCategory): InertiaResponse
    {
        $esc = EntrySourceCategory::whereId($entrySourceCategory->id)->get();

        return Inertia::render('Leads/Edit', [
            'entrySourceCategory' => $esc,
        ]);
    }
}
