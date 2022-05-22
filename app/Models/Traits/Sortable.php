<?php

namespace App\Models\Traits;

trait Sortable
{
    public function scopeSort($query)
    {
        $sortable_fields = $query->getModel()->getFillable();
        $sort = request()->sort;
        $dir = request()->dir;
        //TODO: we need to either convert lots of detail fields to real fields
        //TODO: or figure out a way to handle sorting by those fields
        if (! $sort || ! in_array($sort, $sortable_fields)) {
            return;
        }
        $query->orderBy($sort, $dir);
    }
}
