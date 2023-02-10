<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSort(Builder $query): void
    {
        $request = request();
        //TODO: we need to either convert lots of detail fields to real fields
        //TODO: or figure out a way to handle sorting by those fields
        if (! $request->sort || ! in_array($request->sort, $query->getModel()->getFillable())) {
            return;
        }
        $query->orderBy($request->sort, $request->dir);
    }
}
